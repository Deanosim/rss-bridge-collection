<?php
class BungieNetBridge extends FeedExpander {

	const MAINTAINER = 'Deanosim';
	const NAME = 'Bungie.net News Bridge';
	const URI = 'https://www.bungie.net/en/news';
	const DESCRIPTION = 'RSS Feed for Bungie.net news';
	const PARAMETERS = array();
	const CACHE_TIMEOUT = 10;

    public function collectData(){
        $this->collectExpandableDatas('https://www.bungie.net/en/rss/News');
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
		$article = $articlePage->find('div.image.element.style.background-image', 0);
		// post-content has the actual article
		foreach($articlePage->find('div.content.text-content') as $element)
			$article = $article . $element;

		// --- Fixing ugly elements ---
		//$article->find('url')

		// List of all the crap in the article
		$uselessElements = array(
			'div#article-container script'
		);

		// Remove the listed crap
		foreach($uselessElements as $uslElement) {
			foreach($articlePage->find($uslElement) as $uslElementLoc) {
				$article = str_replace($uslElementLoc, '', $article);
			}
		}

		$item['content'] = $article;    // A unique ID to identify the current item

		return $item;
	}
}
