<?php
class HardDriveBridge extends FeedExpander {

	const MAINTAINER = 'Deanosim';
	const NAME = 'Hard-Drive Bridge';
	const URI = 'https://hard-drive.net/';
	const DESCRIPTION = 'RSS Feed for Hard-Drive.net';
	const PARAMETERS = array();
	const CACHE_TIMEOUT = 10;

    public function collectData(){
        $this->collectExpandableDatas('https://hard-drive.net/feed/');
    }

	protected function parseItem($newsItem){
    	$item = parent::parseItem($newsItem);
    
		// Parse Categories
		$categories = array();
		foreach($newsItem->category as $cat) {
				$categories[] = (string) $cat;
		}

		if (!empty($categories)) {
				$item['categories'] = $categories;
		}

		// Parse uid
		if (!empty($newsItem->guid)) {
					$item['uid'] = (string) $newsItem->guid;
		}

		// --- Recovering the article ---

		// $articlePage gets the entire page's contents
		$articlePage = getSimpleHTMLDOM($newsItem->link);
		// featured-image contain's the main article image
		$article_image = $articlePage->find('img.wp-post-image[src]', 0);
		$article = $articlePage->find('img.wp-post-image[src]', 0);
		        
        // Make a new array
        $enclosures = array();
        
        // Add the article image URL to the array
        // (we don't actually have the URL yet, just the HTML div containing it, but this is close enough for now lol)
        $enclosures[] = $article_image;
        
        // Put the enclosures array into the RSS item
        $item['enclosures'] = $enclosures;
 
		// post-content has the actual article
		foreach($articlePage->find('div.post-content') as $element)
			$article = $article . $element;

		// --- Fixing ugly elements ---

		// List of all the crap in the article
		$uselessElements = array(
			'div.post-content script'
		);

		// Remove the listed crap
		foreach($uselessElements as $uslElement) {
			foreach($articlePage->find($uslElement) as $uslElementLoc) {
				$article = str_replace($uslElementLoc, '', $article);
			}
		}

		$item['content'] = $article;

		return $item;
	}
}
