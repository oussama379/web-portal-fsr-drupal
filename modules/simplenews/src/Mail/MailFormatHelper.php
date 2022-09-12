<?php

namespace Drupal\simplenews\Mail;

use Drupal\Component\Utility\Unicode;

/**
 * Extended mail formatter helpers.
 *
 * @see \Drupal\Core\Mail\MailFormatHelper
 */
class MailFormatHelper {

  /**
   * HTML to text conversion for HTML and special characters.
   *
   * Converts some special HTML characters in addition to
   * \Drupal\Core\Mail\MailFormatHelper\MailFormatHelper::htmlToText().
   *
   * @param string $text
   *   The mail text with HTML and special characters.
   * @param bool $inline_hyperlinks
   *   TRUE: URLs will be placed inline.
   *   FALSE: URLs will be converted to numbered reference list.
   *
   * @return string
   *   The target text with HTML and special characters replaced.
   */
  public static function htmlToText($text, $inline_hyperlinks = TRUE) {
    // By replacing <a> tag by only its URL the URLs will be placed inline
    // in the email body and are not converted to a numbered reference list
    // by MailFormatHelper::htmlToText().
    // URL are converted to absolute URL as drupal_html_to_text() would have.
    if ($inline_hyperlinks) {
      $pattern = '@<a[^>]+?href="([^"]*)"[^>]*?>(.+?)</a>@is';
      $text = preg_replace_callback($pattern, '\Drupal\simplenews\Mail\MailFormatHelper::absoluteMailUrls', $text);
    }

    // Perform standard drupal html to text conversion.
    return \Drupal\Core\Mail\MailFormatHelper::htmlToText($text);
  }

  /**
   * Replaces URLs with absolute URLs.
   */
  public static function absoluteMailUrls($match) {
    global $base_url, $base_path;
    $regexp = &drupal_static(__FUNCTION__);
    $url = $label = '';

    if ($match) {
      if (empty($regexp)) {
        $regexp = '@^' . preg_quote($base_path, '@') . '@';
      }
      list(, $url, $label) = $match;
      $url = strpos($url, '://') ? $url : preg_replace($regexp, $base_url . '/', $url);

      // If the link is formed by Drupal's URL filter, we only return the URL.
      // The URL filter generates a label out of the original URL.
      if (strpos($label, '...') === Unicode::strlen($label) - 3) {
        // Remove ellipsis from end of label.
        $label = Unicode::substr($label, 0, Unicode::strlen($label) - 3);
      }
      if (strpos($url, $label) !== FALSE) {
        return $url;
      }
      return $label . ' ' . $url;
    }
  }

}
