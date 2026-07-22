<?php

/**
 * @package     Jumi
 * @subpackage  com_jumi
 *
 * @copyright   (C) 2026 LennartMart. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Demonstration Jumi application: renders a Blogger feed.
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$blogId    = isset($blogId) ? $blogId : '1748567850225926498';
$login     = isset($login) ? $login : 'joomla-jumi';
$cacheTime = isset($cacheTime) ? (int) $cacheTime : 86400;

$myBlog = new JumiDemoBlog($blogId, $login, $cacheTime);
$myBlog->printAllPosts();

echo '<style type="text/css">
.post {margin:0 0 1.5em;font-family:Verdana,sans-serif;color:#000000;}
.post div {margin:0 0 .75em;line-height:1.3em;}
.post img {padding:4px;border:1px solid #cccccc;}
.post blockquote {margin:1em 20px;}
.post blockquote p {margin:.75em 0;}
.date-header {margin:1.5em 0 0;font-weight:normal;color:#999999;font-size:100%;}
.post-title {margin:0;padding:0;font-size:110%;font-weight:bold;line-height:1.1em;}
.post-title a, .post-title a:visited, .post-title strong {text-decoration:none;color:#333333;font-weight:bold;}
.post-footer {color:#333333;font-size:87%;}
.post-footer .span {margin-right:.3em;}
</style>';

/**
 * Small helper class used by the Blogspot demo application.
 */
class JumiDemoBlog
{
    public $id;
    public $login;
    public $posts;
    public $cacheTime;

    public function __construct($id, $login, $cacheTime)
    {
        $this->id        = $id;
        $this->login     = $login;
        $this->cacheTime = $cacheTime;
        $postsURL        = 'https://www.blogger.com/feeds/' . $id . '/posts/default';
        $fileName        = 'cache/' . md5($postsURL);

        if (file_exists($fileName) && time() - filemtime($fileName) < $this->cacheTime) {
            $this->posts = simplexml_load_string(file_get_contents($fileName));
        } else {
            $feed = @file_get_contents($postsURL);

            if ($feed !== false && strlen($feed) > 1000) {
                @file_put_contents($fileName, $feed);
                $this->posts = simplexml_load_string($feed);
            } elseif (file_exists($fileName)) {
                $this->posts = simplexml_load_string(file_get_contents($fileName));
            } else {
                $this->posts = false;
            }
        }
    }

    public function printAllPosts()
    {
        if (!$this->posts || !isset($this->posts->entry)) {
            echo '<p>No blog posts could be loaded.</p>';

            return;
        }

        echo '<div class="blog-posts">';
        $prev_date = '';

        foreach ($this->posts->entry as $entry) {
            for ($i = 0; $i < 5; $i++) {
                if (isset($entry->link[$i])) {
                    $entry->link[$i] = $entry->link[$i]->attributes();
                }
            }

            if ($prev_date != date('l, F j, Y', strtotime($entry->published))) {
                echo '<h2 class="date-header">' . date('l, F j, Y', strtotime($entry->published)) . '</h2>';
                $prev_date = date('l, F j, Y', strtotime($entry->published));
            }

            // Escape remote feed values before output to avoid stored/reflected XSS from the feed source.
            $href  = htmlspecialchars((string) ($entry->link[0]['href'] ?? ''), ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars((string) $entry->title, ENT_QUOTES, 'UTF-8');

            echo '<div class="post">';
            echo '<h3 class="post-title"><a href="' . $href . '">' . $title . '</a></h3>';
            echo '<div class="post-header-line-1"></div>';
            echo '<div class="post-body">' . strip_tags((string) $entry->content, '<p><br><a><b><i><strong><em><ul><ol><li><blockquote><img><h1><h2><h3><h4>') . '</div>';
            echo '</div>';
        }

        echo '</div>';
    }
}
