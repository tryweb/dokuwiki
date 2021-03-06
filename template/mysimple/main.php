<?php
/**
 * DokuWiki Default Template
 *
 * This is the template you need to change for the overall look
 * of DokuWiki.
 *
 * You should leave the doctype at the very top - It should
 * always be the very first line of a document.
 *
 * @link   http://wiki.splitbrain.org/wiki:tpl:templates
 * @author Andreas Gohr <andi@splitbrain.org>
 */
// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();

include(DOKU_TPLINC.'tpl_functions.php');

/* Define NS Title */
$arr_myTitle = array("scucs"=>"東吳資科 96 碩專非公開網站", "scucs83"=>"東吳電算 83 級系友交流網", "stanley"=>"蔡卓展個人網站", "jerry"=>"蔡卓育個人網站");
list($ns0, $ns1)  = explode(':', $ID);
$my_Title=isset($arr_myTitle[$ns0])?$arr_myTitle[$ns0]:strip_tags($conf['title']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $conf['lang']?>"
 lang="<?php echo $conf['lang']?>" dir="<?php echo $lang['direction']?>">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>
    <?php tpl_pagetitle()?>
    [<?php echo $my_Title;?>]
  </title>

  <?php tpl_metaheaders()?>

  <link rel="shortcut icon" href="<?php echo DOKU_TPL?>images/favicon.ico" />

  <?php /*old includehook*/ @include(dirname(__FILE__).'/meta.html')?>
<?php
if (file_exists(DOKU_PLUGIN.'googleanalytics/code.php')) include_once(DOKU_PLUGIN.'googleanalytics/code.php');
if (function_exists('ga_google_analytics_code')) ga_google_analytics_code();
?>
</head>

<body>
<?php /*old includehook*/ @include(dirname(__FILE__).'/topheader.html')?>
<div class="dokuwiki">

  <b class="rtop_outer">
    <b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b>
  </b>

  <div id="outer_container">

    <?php html_msgarea()?>

    <div class="stylehead">
      <b class="rtop_inner">
        <b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b>
      </b>

      <?php tpl_searchform()?>

      <?php if($conf['breadcrumbs']){?>
      <div class="breadcrumbs">
        <?php tpl_breadcrumbs()?>
        <?php //tpl_youarehere() //(some people prefer this)?>
      </div>
      <?php }?>

      <?php if($conf['youarehere']){?>
      <div class="breadcrumbs">
        <?php tpl_youarehere() ?>
      </div>
      <?php }?>


      <div class="header">
        <div class="logo">
          <?php tpl_link(wl(),$my_Title,'name="dokuwiki__top" id="dokuwiki__top" accesskey="h" title="[ALT+H]"')?>
        </div>
      </div>
<!-- Start FreeOnlineUsers.com -->
<div class="bar-right">
<a href="http://www.freeonlineusers.com">
<script type="text/javascript" src="http://freeonlineusers.com/on1.php?id=54443"> </script> Online Users</a>
</div>
<!-- End FreeOnlineUsers.com -->
    </div>

    <?php flush()?>

    <div id="inner_container">

      <div id="tpl_simple_navi">
        <?php tpl_topbar() ?>
      </div>

      <div class="page">

        <div class="bar" id="bar__top">
          <div class="bar-left" id="bar__topleft">
            <?php tpl_actionlink('edit')?>
          </div>
          <div class="bar-right" id="bar__topright">
            <?php tpl_actionlink('history')?>
            <?php tpl_actionlink('subscription')?>
          </div>
          <div class="clearer"></div>
        </div>



        <!-- wikipage start -->
        <?php tpl_content()?>
        <!-- wikipage stop -->

        <div class="clearer">&nbsp;</div>

        <?php flush()?>

        <div class="stylefoot">

          <?php tpl_actionlink('top')?>

          <div class="meta">
            <div class="user">
              <?php tpl_userinfo()?>
            </div>
            <div class="doc">
              <?php tpl_pageinfo()?>
            </div>
          </div>

          <div class="bar" id="bar__bottom">
            <div class="bar-left" id="bar__bottomleft">
              <?php tpl_actionlink('index')?>
              <?php tpl_actionlink('recent')?>
            </div>
            <div class="bar-right" id="bar__bottomright">
              <?php tpl_actionlink('admin')?>
              <?php tpl_actionlink('profile')?>
              <?php tpl_actionlink('login')?>
            </div>
            <div class="clearer"></div>
          </div>

        </div>
      </div>
    </div>

    <?php /*old includehook*/ @include(dirname(__FILE__).'/pagefooter.html')?>
    <?php /*old includehook*/ @include(dirname(__FILE__).'/footer.html')?>

    <b class="rbottom_inner">
      <b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b>
    </b>

  </div>

  <b class="rbottom_outer">
    <b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b>
  </b>

</div>

<div class="no"><?php /* provide DokuWiki housekeeping, required in all templates */ tpl_indexerWebBug()?></div>
</body>
</html>
