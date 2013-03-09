<?
class Hook
{

    /**
     * Returns an instance of the systemwide Hook object.
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $v = __CLASS__;
            $instance = new $v;
        }
        return $instance;
    }

    protected $registeredEvents = array();


    /**
     * When passed an "event" as a string (e.g. "on_user_add"), a user-defined method can be run whenever this event
     * takes place.
     * <code>
     * Hook::extend('on_user_add', 'MySpecialClass', 'createSpecialUserInfo', 'models/my_special_class.php', array('foo' => 'bar'))
     * </code>
     * @param string $event
     * @param string $class
     * @param string $method
     * @param string $filename
     * @param array $params
     * $param int $priority
     * @return void
     */
    public static function register($event, $class, $method = '', $filename = '', $params = array(), $priority = 5)
    {
        $ce = Hook::getInstance();
        $ce->registeredEvents[$event][] = array(
            $class,
            $method,
            $filename,
            $params,
            $priority
        );
        self::sortByPriority();
    }

    /**
     * An internal function used by Concrete to "fire" a system-wide event. Any time this happens, events that
     * a developer has hooked into will be run.
     * @param string $event
     * @return void
     */
    public static function fire($event)
    {

        // any additional arguments passed to the fire function will get passed FIRST to the method, with the method's own registered
        // params coming at the end. e.g. if I fire Hook::fire('on_login', $userObject) it will come in with user object first
        $args = func_get_args();
        if (count($args) > 1) {
            array_shift($args);
        } else {
            $args = false;
        }

        $ce = Hook::getInstance();
        $events = array();
        if (array_key_exists($event, $ce->registeredEvents)) {
            $events = $ce->registeredEvents[$event];
        }

        $eventReturn = false;

        if (is_array($events) && count($events)) {
            foreach ($events as $ev) {
                $type = $ev[0];
                if ($ev[3] != false) {
                    // HACK - second part is for windows and its paths

                    if (substr($ev[3], 0, 1) == '/' || substr($ev[3], 1, 1) == ':') {
                        // then this means that our path is a full one
                        require_once($ev[3]);
                    } else {
                        require_once(DIR_BASE . '/' . $ev[3]);
                    }
                }
                $params = (is_array($ev[4])) ? $ev[4] : array();

                // now if args has any values we put them FIRST
                if (is_array($args)) {
                    $params = array_merge($args, $params);
                }

                if ($ev[1] instanceof Closure) {
                    $func = $ev[1];
                    $eventReturn = call_user_func_array($func, $params);
                } else {
                    if (method_exists($ev[1], $ev[2])) {
                        // Note: DO NOT DO RETURN HERE BECAUSE THEN MULTIPLE EVENTS WON'T WORK
                        $response = call_user_func_array(array($ev[1], $ev[2]), $params);
                        if (!is_null($response)) {
                            $eventReturn = $response;
                        }
                    }
                }
            }
        }
        return $eventReturn;
    }

    /**
     * Sorts registered events by priority
     * @return void
     */
    protected static function sortByPriority()
    {
        $ce = Hook::getInstance();
        foreach (array_keys($ce->registeredEvents) as $event) {
            usort($ce->registeredEvents[$event],'Hook::comparePriority');
        }
    }

    /**
     * compare function to be used with usort
     * for sorting the events by priority
     * @param array $a
     * @param array $b
     * @return number|number|number
     */
    public static function comparePriority($a, $b)
    {
        if($a[5] > $b[5]) return 1;
        if($a[5] < $b[5]) return -1;
        return 0;
    }

}