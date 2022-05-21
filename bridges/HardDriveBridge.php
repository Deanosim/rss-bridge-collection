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

		// --- Recovering the article ---

		// $articlePage gets the entire page's contents
		$articlePage = getSimpleHTMLDOM($newsItem->link);
		// figure contain's the main article image
		$article = $articlePage->find('div.featured-image', 0);
		// content__article-body has the actual article
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
