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

use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Http\HttpFactory;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$blogId    = isset($blogId) ? (string) $blogId : '1748567850225926498';
$login     = isset($login) ? (string) $login : 'joomla-jumi';
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
        $this->id        = (string) $id;
        $this->login     = (string) $login;
        $this->cacheTime = (int) $cacheTime;
        $this->posts     = false;

        // A Blogger blog id is numeric; refuse anything else so the id cannot
        // alter the request URL (query strings, path traversal, other hosts).
        if (!preg_match('/^\d+$/', $this->id)) {
            return;
        }

        $postsURL = 'https://www.blogger.com/feeds/' . $this->id . '/posts/default';
        $fileName = JPATH_CACHE . '/jumi_blogger_' . md5($postsURL) . '.xml';

        if (is_file($fileName) && time() - filemtime($fileName) < $this->cacheTime) {
            $this->posts = $this->parseFeed((string) file_get_contents($fileName));

            return;
        }

        $feed = $this->fetchFeed($postsURL);

        if ($feed !== null && \strlen($feed) > 1000) {
            if (is_dir(JPATH_CACHE) && is_writable(JPATH_CACHE)) {
                file_put_contents($fileName, $feed);
            }

            $this->posts = $this->parseFeed($feed);
        } elseif (is_file($fileName)) {
            // Fall back to a stale cache copy when the remote fetch fails.
            $this->posts = $this->parseFeed((string) file_get_contents($fileName));
        }
    }

    /**
     * Fetch the remote feed with a bounded timeout.
     *
     * @param   string  $url  The feed URL.
     *
     * @return  string|null  The feed body or null on failure.
     */
    private function fetchFeed(string $url): ?string
    {
        try {
            $response = HttpFactory::getHttp()->get($url, [], 10);
        } catch (\Throwable $e) {
            return null;
        }

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        return (string) $response->getBody();
    }

    /**
     * Parse the feed XML without allowing any network access from the parser.
     *
     * @param   string  $xml  The raw feed XML.
     *
     * @return  \SimpleXMLElement|false
     */
    private function parseFeed(string $xml)
    {
        if ($xml === '') {
            return false;
        }

        return simplexml_load_string($xml, \SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
    }

    public function printAllPosts()
    {
        if (!$this->posts || !isset($this->posts->entry)) {
            echo '<p>No blog posts could be loaded.</p>';

            return;
        }

        // Allow-list based filter: strips disallowed tags, event-handler
        // attributes (onclick, onerror, ...) and javascript: URLs, which
        // strip_tags() alone would let through.
        $bodyFilter = new InputFilter(
            ['p', 'br', 'a', 'b', 'i', 'strong', 'em', 'ul', 'ol', 'li', 'blockquote', 'img', 'h1', 'h2', 'h3', 'h4'],
            ['href', 'src', 'alt', 'title']
        );

        echo '<div class="blog-posts">';
        $prev_date = '';

        foreach ($this->posts->entry as $entry) {
            for ($i = 0; $i < 5; $i++) {
                if (isset($entry->link[$i])) {
                    $entry->link[$i] = $entry->link[$i]->attributes();
                }
            }

            $published = strtotime((string) $entry->published) ?: 0;
            $dayHeader = date('l, F j, Y', $published);

            if ($prev_date !== $dayHeader) {
                echo '<h2 class="date-header">' . $dayHeader . '</h2>';
                $prev_date = $dayHeader;
            }

            // Escape remote feed values before output to avoid stored/reflected XSS from the feed source.
            $href = (string) ($entry->link[0]['href'] ?? '');

            if (!preg_match('#^https?://#i', $href)) {
                $href = '';
            }

            $href  = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars((string) $entry->title, ENT_QUOTES, 'UTF-8');

            echo '<div class="post">';
            echo '<h3 class="post-title"><a href="' . $href . '">' . $title . '</a></h3>';
            echo '<div class="post-header-line-1"></div>';
            echo '<div class="post-body">' . $bodyFilter->clean((string) $entry->content, 'html') . '</div>';
            echo '</div>';
        }

        echo '</div>';
    }
}
