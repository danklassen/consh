<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

/**
 * custom block
 */
class {{ControllerName}} extends BlockController
{
    protected $btTable = '{{TableName}}';

    protected $btInterfaceWidth = "550";
    protected $btInterfaceHeight = "450";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btWrapperClass = 'ccm-ui';

    /**
     * description of the block
     *
     * @return string description of the block
     */
    public function getBlockTypeDescription()
    {
        return t("Enter a description of the block here");
    }

    /**
     * name of the block
     *
     * @return strigng name of the block
     */
    public function getBlockTypeName()
    {
        return t("{{BlockName}}");
    }

    /**
     * validations fired on save
     *
     * @param array $args an array of key => value items to validate
     *
     * @return boolean
     */
    public function validate($args)
    {
        $e = Loader::helper('validation/error');
        $val = Loader::helper('validation/form');
        $val->setData($args);
        //$val->addRequired('field-name', "Error message");
        $val->test();
        $e = $val->getError();
        return $e;
    }

    /**
     * get the content to include in the search index
     *
     * @return string a string representation of the content of this block
     */
    public function getSearchableContent()
    {
        return "";
    }
{{ExtraControllerMethods}}
{{WysiwygContent}}

}