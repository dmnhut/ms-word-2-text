<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Content;
use PhpOffice\PhpWord\IOFactory;

class DocsController extends Controller
{
    public $filePath;

    public function __construct(Request $request)
    {
        $this->filePath = public_path($request->route()->parameter('fileName'));
    }

    public function doc()
    {
        $file['data'] = IOFactory::load($this->filePath, 'MsDoc')->getSections()[0];
        $file['size'] = $file['data']->countElements();
        $file['content'] = '';
        for ($index = 0; $index < $file['size'] - 1; $index++) {
            if (get_class($file['data']->getElement($index)) === 'PhpOffice\PhpWord\Element\Text') {
                $file['content'] .= (new Content($file['data']->getElement($index)))->text;
            }
        }
//        $file['content'] = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", "", $file['content']);
        return $file['content'];
    }

    public function docx()
    {
        $file['content'] = '';
        if (!$this->filePath || !file_exists($this->filePath)) {
            return false;
        }
        $file['zip'] = zip_open($this->filePath);
        if (!$file['zip'] || is_numeric($file['zip'])) {
            return false;
        }
        while ($file['zip_entry'] = zip_read($file['zip'])) {
            if (zip_entry_open($file['zip'], $file['zip_entry']) == FALSE) {
                continue;
            }
            if (zip_entry_name($file['zip_entry']) != "word/document.xml") {
                continue;
            }
            $file['content'] .= zip_entry_read($file['zip_entry'], zip_entry_filesize($file['zip_entry']));
            zip_entry_close($file['zip_entry']);
        }
        zip_close($file['zip']);
        $file['content'] = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $file['content']);
        $file['content'] = str_replace('</w:r></w:p>', "\r\n", $file['content']);
        $file['content'] = strip_tags($file['content']);
        return $file['content'];
    }
}
