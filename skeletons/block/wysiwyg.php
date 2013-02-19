    public function getContent()
    {
        $content = $this->translateFrom($this->{{wysiwyg_field}});

        return $content;
    }

    public function br2nl($str)
    {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("<br />\n", "\n", $str);
        return $str;
    }

    public function getContentEditMode()
    {
        $content = $this->translateFromEditMode($this->{{wysiwyg_field}});
        return $content;
    }

    public function translateFromEditMode($text)
    {
        // old stuff. Can remove in a later version.
        $text = str_replace('href="{[CCM:BASE_URL]}', 'href="' . BASE_URL . DIR_REL, $text);
        $text = str_replace('src="{[CCM:REL_DIR_FILES_UPLOADED]}', 'src="' . BASE_URL . REL_DIR_FILES_UPLOADED, $text);

        // we have the second one below with the backslash due to a screwup in the
        // 5.1 release. Can remove in a later version.

        $text = preg_replace(
        array(
          '/{\[CCM:BASE_URL\]}/i',
          '/{CCM:BASE_URL}/i'),
        array(
          BASE_URL . DIR_REL,
          BASE_URL . DIR_REL)
        , $text);

        // now we add in support for the links

        $text = preg_replace(
        '/{CCM:CID_([0-9]+)}/i',
        BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=\\1',
        $text);

        // now we add in support for the files

        $text = preg_replace_callback(
        '/{CCM:FID_([0-9]+)}/i',
        array('{{ControllerName}}', 'replaceFileIDInEditMode'),
        $text);


        return $text;
    }

    public function translateFrom($text)
    {
        // old stuff. Can remove in a later version.
        $text = str_replace('href="{[CCM:BASE_URL]}', 'href="' . BASE_URL . DIR_REL, $text);
        $text = str_replace('src="{[CCM:REL_DIR_FILES_UPLOADED]}', 'src="' . BASE_URL . REL_DIR_FILES_UPLOADED, $text);

        // we have the second one below with the backslash due to a screwup in the
        // 5.1 release. Can remove in a later version.

        $text = preg_replace(
        array(
          '/{\[CCM:BASE_URL\]}/i',
          '/{CCM:BASE_URL}/i'),
        array(
          BASE_URL . DIR_REL,
          BASE_URL . DIR_REL)
        , $text);

        // now we add in support for the links

        $text = preg_replace_callback(
        '/{CCM:CID_([0-9]+)}/i',
        array('{{ControllerName}}', 'replaceCollectionID'),
        $text);

        // now we add in support for the files

        $text = preg_replace_callback(
        '/{CCM:FID_([0-9]+)}/i',
        array('{{ControllerName}}', 'replaceFileID'),
        $text);

        return $text;
    }

    private function replaceFileID($match)
    {
        $fID = $match[1];
        if ($fID > 0) {
            $path = File::getRelativePathFromID($fID);
            return $path;
        }
    }

    private function replaceFileIDInEditMode($match)
    {
        $fID = $match[1];
        return View::url('/download_file', 'view_inline', $fID);
    }

    private function replaceCollectionID($match)
    {
        $cID = $match[1];
        if ($cID > 0) {
            $path = Page::getCollectionPathFromID($cID);
            if (URL_REWRITING == true) {
                $path = DIR_REL . $path;
            } else {
                $path = DIR_REL . '/' . DISPATCHER_FILENAME . $path;
            }
            return $path;
        }
    }

    public function translateTo($text)
    {
        // keep links valid
        $url1 = str_replace('/', '\/', BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME);
        $url2 = str_replace('/', '\/', BASE_URL . DIR_REL);
        $url3 = View::url('/download_file', 'view_inline');
        $url3 = str_replace('/', '\/', $url3);
        $url3 = str_replace('-', '\-', $url3);
        $text = preg_replace(
        array(
          '/' . $url1 . '\?cID=([0-9]+)/i',
          '/' . $url3 . '([0-9]+)\//i',
          '/' . $url2 . '/i'),
        array(
          '{CCM:CID_\\1}',
          '{CCM:FID_\\1}',
          '{CCM:BASE_URL}')
        , $text);
        return $text;
    }

    public function save($data)
    {
        $args = $data;

        $content = $this->translateTo($data['{{wysiwyg_field}}']);
        $args['{{wysiwyg_field}}'] = $content;
        parent::save($args);
    }