<?php

require_once 'Zend/Feed/Rss.php';
require_once 'Zend/Exception.php';
require_once 'Zend/Search/Lucene/Field.php';
require_once 'Zend/Search/Lucene/Document.php';
require_once 'Zend/Feed.php';
require_once 'Zend/Feed/Atom.php';

/**
 * 
 *
 */
class Zend_Search_Lucene_Feed extends Zend_Search_Lucene_Document
{
    
    /**
     * @var string $url
     */
    private $url;
    
    /**
     * @var string $id
     */
    private $id;
    
    /**
     * @param string $filename
     */
    public function __construct($id, $url, $filename)
    {
        $this->url = $url;
        $this->id = $id;
        
        $feed = Zend_Feed::importFile($filename);
        
        if ($feed instanceof Zend_Feed_Rss) {
            $this->processRss($feed);
        } else if ($feed instanceof Zend_Feed_Atom) {
            $this->processAtom($feed);
        } else {
            throw new Zend_Exception("Error reading feed " . $filename);
        }
    }
    
    /**
     * @param Zend_Feed_Rss $rss
     */
    private function processRss($rss)
    {
    	$this->addField(Zend_Search_Lucene_Field::UnIndexed('id', $this->id));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('url', $this->url));
        
        $this->addField(Zend_Search_Lucene_Field::Text('title', $rss->title()));
        $this->addField(Zend_Search_Lucene_Field::Text('description', $rss->description()));
        // Loop over each channel item and store relevant data
        $text = '';
        foreach ($rss as $item) {
            $text .=  $item->title();
            $text .=  ' ';
            $text .=  $item->description();
        }
        $this->addField(Zend_Search_Lucene_Field::Text('body', $text));
    }

    /**
     * @param Zend_Feed_Atom $atom
     */
    private function processAtom($atom)
    {
    	$this->addField(Zend_Search_Lucene_Field::UnIndexed('id', $this->id));    	
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('url', $this->url));
        
        $this->addField(Zend_Search_Lucene_Field::Text('title', $atom->title()));
        // Loop over each channel item and store relevant data
        $text = '';        
        foreach ($atom as $item) {
            $text .=  $item->title();
            $text .=  ' ';
            $text .=  $item->content();
        }
        $this->addField(Zend_Search_Lucene_Field::Text('body', $text));
    }

    /**
     * Load HTML document from a file
     *
     * @param string $file
     * @return Zend_Search_Lucene_Feed
     */
    public static function loadFeedFile($id, $url, $file)
    {
        return new Zend_Search_Lucene_Feed($id, $url, $file);
    }
}

?>
