<?php

class TemplateCommons {
    public $template_dir_map=array("layout"=>"layout","structure"=>"structures","page_template"=>"page_template");
    private $template_dir="";

    /**
     * Constructor
     * @access  public
     * @param   string $template_dir     path of the template folder
     * @author  SupunGS
     */
    public function __construct($template_path) {
        $this->template_dir=$template_path."/";
    }

    /**
     * Creates a folder for a new template within the templates directory.
     * @access  public
     * @param   string $template_type   type of the new template
     * @param   string $template_name   name of the new template
     * @return  string     folder name for the new template
     * @author  SupunGS
     */
    public function create_template_dir($template_type,$template_name) {
        $template_dir=$this->get_folder_name($template_name);
        $full_dir=$this->template_dir . $this->template_dir_map[$template_type]."/".$template_dir;
        if (!file_exists($full_dir)) {
            mkdir($full_dir);
            return $template_dir;
        }else {
            $i=1;
            while($this->template_exists($template_type,$template_dir.$i)) {
                $i=$i+1;
            }
            mkdir($full_dir.$i);
            return $template_dir.$i;
        }
    }

    /**
     * Save an dom object into an xml file
     * @access  public
     * @param   object $xml     DOMDocument object to save
     * @param   string $directory     directory to save the file
     * @param   string $file_name     file name to save
     * @return  boolean         whether the file saving was successful
     * @author  SupunGS
     */
    public function save_xml($xml,$directory,$file_name) {
        $xml->formatOutput = true;
        $full_dir=$this->template_dir.$directory;   //  "../templates/".$directory;
        //        if (!file_exists($full_dir)) {
        //            mkdir($full_dir);
        //        }
        return $xml->save($full_dir."/".$file_name) ;
    }

    public function parse_to_XML($xml_str) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = FALSE;
        $dom->loadXML($xml_str);
        return $dom;
    }

    /**
     * Create the template matadata file that specifies metadata for the template
     * @access  public
     * @param   string $template_type   type of the template. layout, structure, page_template
     * @param   string $template_name   name of the template
     * @param   string $template_desc   description about the template
     * @param   string $maintainer_name     name of the template maintainer
     * @param   string $maintainer_email    email of the template maintainer
     * @param   string $template_url   url for the template website
     * @param   string $template_license    license information about the template
     * @param   string $release_version     release version of the template
     * @param   string $release_date    release date of the template
     * @param   string $release_state   release state of the template
     * @param   string $release_notes   release notes of the template
     * @return  boolean  whether the metadata file created successfully
     * @author  SupunGS
     */
    public function create_template_metadata($template_type,$template_folder, $template_name,$template_desc,$maintainer_name,
        $maintainer_email,$template_url,$template_license,$release_version,$release_date,$release_state,$release_notes) {
        global $template_dir_map;
        $metadata = new DOMDocument();
        $metadata->formatOutput = true;

        $template_node = $metadata->createElement($template_type);
        $template_node->setAttribute("version", "0.1");
        $metadata->appendChild( $template_node );

        $name_node=$metadata->createElement("name");
        $name_node->appendChild($metadata->createTextNode($template_name));
        $template_node->appendChild( $name_node );

        $desc_node=$metadata->createElement("description");
        $desc_node->appendChild($metadata->createTextNode($template_desc));
        $template_node->appendChild( $desc_node );

        $maints_node=$template_node->appendChild( $metadata->createElement("maintainers") );
        $maintainer_node=$maints_node->appendChild($metadata->createElement("maintainer"));

        $maint_name_node=$maintainer_node->appendChild($metadata->createElement("name"));
        $maint_email_node=$maintainer_node->appendChild($metadata->createElement("email"));
        $maint_name_node->appendChild($metadata->createTextNode($maintainer_name));
        $maint_email_node->appendChild($metadata->createTextNode($maintainer_email));

        $url_node=$template_node->appendChild( $metadata->createElement("url") );
        $url_node->appendChild($metadata->createTextNode($template_url));

        $license_node=$template_node->appendChild( $metadata->createElement("license") );
        $license_node->appendChild($metadata->createTextNode($template_license));

        $release_node=$template_node->appendChild( $metadata->createElement("release") );
        $rel_version_node=$release_node->appendChild($metadata->createElement("version"));
        $rel_date_node=$release_node->appendChild($metadata->createElement("date"));
        $rel_state_node=$release_node->appendChild($metadata->createElement("state"));
        $rel_notes_node=$release_node->appendChild($metadata->createElement("notes"));
        $rel_version_node->appendChild($metadata->createTextNode($release_version));
        $rel_date_node->appendChild($metadata->createTextNode($release_date));
        $rel_state_node->appendChild($metadata->createTextNode($release_state));
        $rel_notes_node->appendChild($metadata->createTextNode($release_notes));

        $template_dir=$this->template_dir_map[$template_type]."/".$template_folder;
        return $this->save_xml($metadata,$template_dir,$template_type.".xml");
    }

    /**
     * Upload an image file to a given directory
     * @access  public
     * @param   string $directory   directory to save the file
     * @return  boolean     whether uploading successed or not
     * @author  SupunGS
     */
    public function upload_image($directory) {
        $allowedExts = array("gif", "jpeg", "jpg", "png");
        $extension = end(explode(".", $_FILES["file"]["name"]));
        if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg")
            || ($_FILES["file"]["type"] == "image/jpg")|| ($_FILES["file"]["type"] == "image/pjpeg")
            || ($_FILES["file"]["type"] == "image/x-png")  || ($_FILES["file"]["type"] == "image/png"))
            && ($_FILES["file"]["size"] < 100000) && in_array($extension, $allowedExts)) {
        }
        else return false;

        if ($_FILES["file"]["error"] ==0) {
            move_uploaded_file($_FILES["file"]["tmp_name"], $_FILES["file"]["name"]);
            echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
        }
    }

    /**
     * Check whether a template already exists with a given name and type
     * @access  public
     * @param   string $template_type   type of the template. layout, structure, page_template
     * @param   string $template_name   name of the template to check
     * @return  boolean     whether the template already exists or not
     * @author  SupunGS
     */
    public function template_exists($template_type,$template_name) {
    //$full_dir="../templates/".$this->template_dir_map[$template_type]."/".$template_name;
        $full_dir=$this->template_dir . $this->template_dir_map[$template_type]."/".$template_name;
        return file_exists($full_dir);
    }

    /**
     * get a list of existing templates of a given type
     * @access  public
     * @param   string $template_type   type of template: layout, structure, page_template
     * @return  array     array of existing templates
     * @author  SupunGS
     */
    public function get_template_list($template_type) {
        $dir=realpath($this->template_dir . $this->template_dir_map[$template_type]);
        $dir_list= scandir($dir);
        $list = array();
        foreach ($dir_list as $item) {
            $check_dir=$dir .'/'.$item;
            $xml_file=$check_dir.'/'.$template_type.'.xml';
            if(is_dir($check_dir) && is_file($xml_file)) {
                $xml = simplexml_load_file($xml_file);
                $template_name=trim($xml->name);
                $list[$item]=$template_name;
            }
        }
        return $list;
    }

    /**
     * load metadata of a given template
     * @access  public
     * @param   string $template_type   type of the template. layout, structure, page_template
     * @param   string $template_name   name of the template
     * @return  array     metadata of the specified template
     * @author  SupunGS
     */
    public function load_metadata($template_type,$template_name) {
        $xmlpath=$this->template_dir. $this->template_dir_map[$template_type]."/".$template_name."/".$template_type.".xml";
        $xmlDoc = new DOMDocument();
        $xmlDoc =simplexml_load_file($xmlpath);
        $metadata=array();
        //        $metadata['template_name']=$xmlDoc->getElementsByTagName('name')->item(0)->nodeValue;
        $metadata['template_type']=$template_type;
        $metadata['template_name']=trim($xmlDoc->name);
        $metadata['template_desc']=trim($xmlDoc->description);

        $metadata['maintainer_name']=trim($xmlDoc->maintainers->maintainer->name);
        $metadata['maintainer_email']=trim($xmlDoc->maintainers->maintainer->email);

        $metadata['template_url']=trim($xmlDoc->url);
        $metadata['template_license']=trim($xmlDoc->license);

        $metadata['release_version']=trim($xmlDoc->release->version);
        $metadata['release_date']=trim($xmlDoc->release->date);
        $metadata['release_state']=trim($xmlDoc->release->state);
        $metadata['release_notes']=trim($xmlDoc->release->notes);

        return $metadata;
    }

    /**
     * Returns the folder name for a new template
     * @access  public
     * @param   string $template_name   name of the template
     * @return  boolean     folder name for the new template after removing whitespaces
     * @author  SupunGS
     */
    public function get_folder_name($temlate_name) {
        $temlate_name=trim($temlate_name);
        $temp=preg_replace("/[^A-Za-z0-9 ]/", '', $temlate_name);
        return str_replace(" ", "_", $temp);
    }

    /**
     * delete a specified template
     * @access  public
     * @param   string $template_type   type of the template. layout, structure, page_template
     * @param   string $template_name   name of the template
     * @author  SupunGS
     */
    public function delete_template($template_type,$template_name) {
        $full_dir=$this->template_dir . $this->template_dir_map[$template_type]."/".$template_name;
        $this->remove_dir_content($full_dir);
    }
    
    /**
     * remove a folder and its content
     * @access  public
     * @param   string $directory   directory to remove
     * @author  SupunGS
     */
    private function remove_dir_content($directory) {
        foreach(glob($directory . '/*') as $file) {
            if(is_dir($file)) rrmdir($file); else unlink($file);
        } rmdir($directory);
    }
}
?>
