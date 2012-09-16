<?php

namespace Lib {
	
	use stdClass;
	
	define('HISTOGRAM_BUCKETS', 4);
	define('HISTORGAM_GRANULARITY', 256 / HISTOGRAM_BUCKETS);
	
	class Awwnime {
		
		/**
		 * Loads a file, determines the image type by scanning the header, and returns a GD object
		 * @param string $file Path to the file to load
		 * @return object Object containing the GD image and the mimeType, null on failure
		 */
		public static function loadImage($file) {

			$retVal = null;
			
			$type = self::getImageType($file);
			
			if (false !== $type) {
				$retVal = new stdClass;
				$retVal->mimeType = $type;
				switch ($type) {
					case 'image/jpeg':
						$retVal->image = imagecreatefromjpeg($file);
						break;
					case 'image/png':
						$retVal->image = imagecreatefrompng($file);
						break;
					case 'image/gif':
						$retVal->image = imagecreatefromgif($file);
						break;
				}
			}
			
			return $retVal;
			
		}
		
		/**
		 * Given a URL, downloads and saves the output. Does some special case processing depending on where the image is hosted
		 * @param string $url URL to download
		 * @param string $fileName File path to save to
		 * @return bool Whether the image was downloaded successfully
		 */
		public static function downloadImage($url, $fileName) {

			$retVal = false;
			
			$url = self::_parseUrl($url);
			if ($url) {
				$file = self::curl_get_contents($url);
				if (null != self::_getImageType($file)) {
					$handle = fopen($fileName, 'wb');
					if ($handle) {
						fwrite($handle, $file);
						fclose($handle);
						$retVal = true;
					}
				}
			}

			return $retVal;

		}
		
		/**
		 * Generates a simplified histogram from the provided image
		 */
		public static function generateHistogram($file) {
			
			$retVal = null;
			$img = self::loadImage($file);
			
			if (null != $img && $img->image) {
				
				$img = $img->image;
				$resampled = imagecreatetruecolor(256, 256);
				imagecopyresampled($resampled, $img, 0, 0, 0, 0, 256, 256, imagesx($img), imagesy($img));
				imagedestroy($img);
			
				$width = imagesx($resampled);
				$height = imagesy($resampled);
				$total = $width * $height;
				$red = array(0, 0, 0, 0);
				$green = array(0, 0, 0, 0);
				$blue = array(0, 0, 0, 0);
				for ($x = 0; $x < $width; $x++) {
					for ($y = 0; $y < $height; $y++) {
						$c = imagecolorat($resampled, $x, $y);
						$red[floor(($c >> 16) / HISTORGAM_GRANULARITY)]++;
						$green[floor(($c >> 8 & 0xff) / HISTORGAM_GRANULARITY)]++;
						$blue[floor(($c & 0xff) / HISTORGAM_GRANULARITY)]++;
					}
				}
				imagedestroy($resampled);
				
				$retVal = new stdClass;
				$retVal->red = array();
				$retVal->green = array();
				$retVal->blue = array();
				for ($i = 0; $i < HISTOGRAM_BUCKETS; $i++) {
					$retVal->red[] = $red[$i] / $total;
					$retVal->green[] = $green[$i] / $total;
					$retVal->blue[] = $blue[$i] / $total;
				}
				
			}
			
			return $retVal;

		}
		
		/**
		 * Given an image file, finds similar images in the database
		 * @param string $file Path or URL to the file to check against
		 * @param int $limit Number of matches to return
		 * @return array Array of matched posts, null on error
		 */
		public static function findSimilarImages($file, $limit = 5) {
			
			$limit = !is_int($limit) ? 5 : $limit;
			$retVal = null;
			
			$histogram = self::generateHistogram($file);
			if (null !== $histogram) {
				
				$query = '';
				$params = array();
				for ($i = 1; $i <= HISTOGRAM_BUCKETS; $i++) {
					$params[':red' . $i] = $histogram->red[$i - 1];
					$params[':green' . $i] = $histogram->green[$i - 1];
					$params[':blue' . $i] = $histogram->blue[$i - 1];
					$query .= 'ABS(post_hist_r' . $i . ' - :red' . $i . ') + ABS(post_hist_g' . $i . ' - :green' . $i . ') + ABS(post_hist_b' . $i . ' - :blue' . $i . ') + ';
				}
				
				// Find the top five most similar images in the database
				$result = Db::Query('SELECT post_id, post_title, post_link, post_date, ' . $query . '0 AS distance FROM posts WHERE post_hist_r1 IS NOT NULL ORDER BY distance LIMIT ' . $limit, $params);
				if ($result) {
				
					$retVal = array();
					while($row = Db::Fetch($result)) {
						$obj = new stdClass;
						$obj->id = $row->post_id;
						$obj->title = $row->post_title;
						$obj->url = self::_parseUrl($row->post_link);
						$obj->similarity = abs(100 - (100 * ($row->distance / 12)));
						$obj->date = $row->post_date;
						$retVal[] = $obj;
					}
					
				}
			
			}
			
			return $retVal;
		
		}
		
		/**
		 * Given a file, returns the image mime type
		 */
		public static function getImageType($fileName) {
			$retVal = null;
			if (is_readable($fileName)) {
				$handle = fopen($fileName, 'rb');
				$head = fread($handle, 10);
				$retVal = self::_getImageType($head);
				fclose($handle);
			}
			return $retVal;
		}
		
		/**
		 * Determines the image type of the incoming data
		 * @param string $data Data of the image file to determine
		 * @return string Mime type of the image, null if not recognized
		 */
		private static function _getImageType($data) {
		
			$retVal = null;
			if (ord($data{0}) == 0xff && ord($data{1}) == 0xd8) {
				$retVal = 'image/jpeg';
			} else if (ord($data{0}) == 0x89 && substr($data, 1, 3) == 'PNG') {
				$retVal = 'image/png';
			} else if (substr($data, 0, 6) == 'GIF89a' || substr($data, 0, 6) == 'GIF87a') {
				$retVal = 'image/gif';
			}
			
			return $retVal;
		
		}
		
		private static function _parseUrl($url) {
			
			$urlInfo = parse_url($url);
			
			if ($urlInfo !== false) {
				// Handle deviantArt submissions
				if (strpos($url, 'deviantart.com') !== false) {
					$info = json_decode(self::curl_get_contents('http://backend.deviantart.com/oembed?url=' . urlencode($url)));
					if (is_object($info)) {
						$url = $info->url;
					}
				
				// Handle imgur images that didn't link directly to the image
				} elseif ($urlInfo['host'] == 'imgur.com' && strpos($urlInfo['path'], '.') === false) {
					$url .= '.jpg';
				}
			}
			
			return $url;
		
		}
		
		/**
		 * A drop in replacement for file_get_contents. Changes the user-agent to make reddit happy
		 * @param string $url Url to retrieve
		 * @return string Data received
		 */
		function curl_get_contents($url) {
			$c = curl_init($url);
			curl_setopt($c, CURLOPT_USERAGENT, 'moe downloader by /u/dxprog');
			curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			return curl_exec($c);
		}
	
	}

}