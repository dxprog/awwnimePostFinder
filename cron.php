<?php

define('__DEBUG__', true);
define('__INCLUDE__', '/var/www/awwnime/repost/');

define('POSTS_IMPORT', true);
define('POSTS_PROCESS', true);

require(__INCLUDE__ . 'config.php');
require(__INCLUDE__ . 'lib/db.php');
require(__INCLUDE__ . 'lib/awwnime.php');

function debug($text) {
	if (__DEBUG__) {
		echo $text, PHP_EOL;
	}
}

Lib\Db::Connect('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASS);

if (POSTS_IMPORT) {
	// Grab the last five pages of posts
	debug('-- Beginning post import --');
	$afterId = null;
	for ($i = 0; $i < 5; $i++) {
		debug('Retrieving page ' . ($i + 1) . '...');
		$posts = Lib\awwnime::getAwwnimePage($i, $afterId);
		if (is_object($posts)) {
			$afterId = isset($posts->after) ? $posts->after : null;
			// Loop through all the posts and add/update them in the database
			foreach ($posts->children as $post) {
			
				$post = $post->data;
				
				// Ignore self-posts
				if ($post->domain !== 'aelf.awwnime') {
					
					debug('-- ' . $post->title . ' -- [' . $post->score . ']');
					
					$params = array( ':id' => $post->id );
					$result = Lib\Db::Query('SELECT post_id AS total FROM posts WHERE post_id = :id', $params);
					if ($result) {
						$params[':score'] = $post->score;
						$params[':updated'] = time();
						$query = '';
						if ($result->count > 0) {
							$query = 'UPDATE posts SET post_score = :score, post_updated = :updated WHERE post_id = :id';
							debug('Updating...');
						} else {
							$params[':title'] = $post->title;
							$params[':link'] = $post->url;
							$params[':poster'] = $post->author;
							$params[':date'] = $post->created_utc;
							$params[':flair'] = (string)$post->link_flair_text;
							$params[':keywords'] = Lib\awwnime::getKeywords($post->title . ' ' . $post->link_flair_text);
							$query = 'INSERT INTO posts (post_id, post_title, post_link, post_poster, post_date, post_flair, post_keywords, post_score, post_updated) VALUES (:id, :title, :link, :poster, :date, :flair, :keywords, :score, :updated)';
							debug('Inserting...');
						}
						
						Lib\Db::Query($query, $params);
						
					}
					
				}
			
			}
			
		}
		
		// Sleep a moment to keep within API request specs
		sleep(2);
		
	}

	debug('-- POST IMPORT COMPLETE --');
}

debug('--------------------------');

if (POSTS_PROCESS) {
	debug('--   PROCESSING POSTS   --');
	// Process all unprocessed posts
	$result = Lib\Db::Query('SELECT post_id, post_link FROM posts WHERE post_processed = 0');
	if ($result && $result->count > 0) {
		
		debug($result->count . ' posts to process...');
		while ($row = Lib\Db::Fetch($result)) {
			
			$processed = false;
			
			// Check for imgur album
			if (preg_match('/imgur\.com\/a\/([\w]+)/i', $row->post_link, $matches)) {
				debug('Processing imgur album [' . $matches[1] . ']...');
				$images = Lib\awwnime::getImgurAlbum($matches[1]);
				if ($images) {
					debug(count($images) . ' images in album...');
					foreach ($images as $image) {
						debug('Processing ' . $image . '...');
						Lib\awwnime::processImage($row->post_id, $image);
						$processed = true;
					}
				}
			} else {
				if (strpos($row->post_link, 'youtube.com') === false && strpos($row->post_link, 'reddit.com') === false) {
					debug('Processing ' . $row->post_link . '...');
					Lib\awwnime::processImage($row->post_id, $row->post_link);
					$processed = true;
				}
			}
			
			Lib\Db::Query('UPDATE posts SET post_processed = 1 WHERE post_id = :id', array( ':id' => $row->post_id ));
			
		}
		
	}
}