<?php

namespace App\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;
use DOMDocument;

class LeerFicheros {
    
    private $id;
    private $rutaFisica;
    private $extension;
    private $nombre;

    public function convertToText($fichero,$arrayExtensionesLeer) {
        
        $this->id = $fichero->getId();
        $this->rutaFisica = $fichero->getRuta();
        $this->extension = $fichero->getExtension();
        $this->nombre = $fichero->getNombre();
        
        if(isset($this->rutaFisica) && !file_exists($this->rutaFisica)) {
            return 'No existe el fichero físico con el ID: '.$this->id;
        }

        if (in_array($this->extension, $arrayExtensionesLeer)) {
            switch ($this->extension) {
                case 'doc':
                    return $this->read_doc();
                    break;
                case 'docx':
                    return $this->read_docx();
                    break;
                case 'xls':
                    return $this->xlsx_to_text();
                    break;
                case 'pptx':
                    return $this->pptx_to_text();
                    break;
                case 'xml':
                    return $this->xml_to_text();
                    break;
                case 'txt':
                    return $this->txt_to_text();
                    break;
                default:
                    return "Extension no soportada";
                    break;
            }
        }
    }

    public function read_doc() {
        $fileHandle = fopen($this->rutaFisica, "r");
        $line = @fread($fileHandle, filesize($this->rutaFisica));   
        $lines = explode(chr(0x0D),$line);
        $outtext = "";
        foreach($lines as $thisline)
          {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE)||(strlen($thisline)==0))
              {
              } else {
                $outtext .= $thisline." ";
              }
          }
         $outtext = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","",$outtext);
        return $outtext;
    }

    public function read_docx(){

        $striped_content = '';
        $content = '';

        $zip = zip_open($this->rutaFisica);

        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }// end while

        zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

    public function xlsx_to_text(){
        $xml_filename = "xl/sharedStrings.xml"; //content file name
        $zip_handle = new ZipArchive;
        $output_text = "";
        if(true === $zip_handle->open($this->rutaFisica)){
            if(($xml_index = $zip_handle->locateName($xml_filename)) !== false){
                $xml_datas = $zip_handle->getFromIndex($xml_index);
                $xml_handle = new DOMDocument();
                $xml_handle->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $output_text = strip_tags($xml_handle->saveXML());
            }else{
                $output_text .="";
            }
            $zip_handle->close();
        }else{
        $output_text .="";
        }
        return $output_text;
    }

    public function pptx_to_text(){
        $zip_handle = new ZipArchive;
        $output_text = "";
        if(true === $zip_handle->open($this->rutaFisica)){
            $slide_number = 1; //loop through slide files
            while(($xml_index = $zip_handle->locateName("ppt/slides/slide".$slide_number.".xml")) !== false){
                $xml_datas = $zip_handle->getFromIndex($xml_index);
                $xml_handle = new DOMDocument();
                $xml_handle->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                $output_text .= strip_tags($xml_handle->saveXML());
                $slide_number++;
            }   
            if($slide_number == 1){
                $output_text .="";
            }
            $zip_handle->close();
        }else{
        $output_text .="";
        }
        return $output_text;
    }

    public function xml_to_text(){

        $fichero = file_get_contents($this->rutaFisica, FILE_USE_INCLUDE_PATH);
        return $fichero;
    }
    
    public function txt_to_text(){
        
        $fichero = file_get_contents($this->rutaFisica, FILE_USE_INCLUDE_PATH);
        return $fichero;
    }
}
