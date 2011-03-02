<?
require './res/markdown.php';
$md = new MarkdownExtra_Parser;
function mdown($path) {
  global $md;
  return $md->transform(file_get_contents($path));
}
function mdown_str($str) {
  global $md;
  return $md->transform($str);
}
function mdown_m($m) {
  $md = new MarkdownExtra_Parser;
  $html = $md->transform($m[2]);
  return $m[1] . '>' . $html . '</wrapper>';
}
function mdown_output_filter($buffer, $flags) {
  $buffer = preg_replace_callback(
      '/[ \t\r\n]+type="text\/markdown"([^>]*)>(.+)<\/wrapper>/smU',
      'mdown_m', $buffer);
  return $buffer;
}
ob_start('mdown_output_filter', 0);

?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Move</title>
    <meta name="viewport" content="width=840">
    <script src="move.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
    <script>__move.debug = true;</script>
    <script type="text/move">

#md2html = (new Showdown.converter).makeHtml
#$('*[type=text/markdown]').each(^{ e = $ this; e.html md2html e.html() })
#if (window.onDidMarkdown) window.onDidMarkdown()

// Menu
$menu = $ '#menu'
$menu.find('a[href="#"]').last().click ^{ window.scrollTo 0, 0 }

// Sticky menu
$menu.$wrapper = $menu.find('> wrapper').first()
initialOrigin = $menu.offset()
menuTop = initialOrigin.top
sectionAfterMenu = $menu.next()
sectionAfterMenu.topMargin = parseInt sectionAfterMenu.css 'margin-top'
pageYOffset = (window.pageYOffset != undefined) ? ^{ window.pageYOffset }
                                                : ^{ window.scrollY }
menuIsFloating = false
updateMenuOrigin = ^{
  y = menuTop - pageYOffset()
  if (y >= 0) {
    if (menuIsFloating) {
      menuIsFloating = false
      $menu.css {
        position: 'static',
        width: 'auto'
      }
      sectionAfterMenu.css {'margin-top': sectionAfterMenu.topMargin + 'px'}
      $menu.removeClass 'floating'
    }
  } else {
    if (!menuIsFloating) {
      sectionAfterMenu.css {
        'margin-top': (sectionAfterMenu.topMargin + $menu.height()) + 'px'
      }
      $menu.addClass 'floating'
      menuIsFloating = true
    }
    $menu.css {
      position: 'fixed',
      top: Math.max(0, menuTop - pageYOffset()) + 'px',
      left: '-8px',
      width: window.innerWidth + 'px'
    }
  }
}

updateMenuOrigin()
$(window).bind 'scroll', updateMenuOrigin

    </script>
    <link rel="stylesheet" href="res/screen.css" type="text/css" media="all">
  </head>
  <body>
    <header>
      <wrapper>
        <h1><a href="/"><span>Move</span></a></h1>
      </wrapper>
      <wrapper type="text/markdown">
Move is a modern and simple *programming language* which can run on virtually
any computer. Move is primarily aimed toward people not previously familiar with
programming computers.
      </wrapper>
    </header>
    <section class="example">
      <wrapper type="text/markdown">
Here is a simple Move program which outputs "Hello John" three times:

    hello = ^(name){ "Hello "+name }
    repeat {times: 3} ^{
      print hello {name: "John"}
    }

      </wrapper>
    </section>
    <section id="menu">
      <wrapper>
        <menu-items>
        <a href="#how-to-move" title="An introduction to programming">How to Move</a>
        <a href="#library" title="Built-in objects and functions"><strong>❖</strong> Library</a>
        <a href="#language" title="Language reference"><strong>✏</strong> Language</a>
        <a href="/try/" title="Try Move in an interactive console">Try it</a>
        <!--a href="#about" title="About this project, why Move exist, etc">About</a-->
        <a href="#" title="Jump to the top of the scroll">Top &uarr;</a>
        </menu-items>
      </wrapper>
    </section>
    <section class="body">
      <wrapper>
<?= mdown('how-to-move.md') ?>
      </wrapper>
    </section>
    <section class="body">
      <wrapper>
<?= mdown('library.md') ?>
      </wrapper>
    </section>
    <section class="body">
      <wrapper>
<?= mdown('language.md') ?>
      </wrapper>
    </section>
    <!--section id="about" class="body">
      <wrapper type="text/markdown">
# About Move

TODO About the project, why, etc

      </wrapper>
    </section-->
    <footer>
      <wrapper type="text/markdown">
Move is free software and
[open source](https://github.com/rsms/move)
under a permissive license, created by <a href="http://rsms.me/">Rasmus Andersson</a>.
      </wrapper>
    </footer>
    <script>

$(function(){
  // curry pre < code to be able to get prettified
  var n,i,L,a,v = document.getElementsByTagName('pre');
  for (i=0,L=v.length;i<L;++i) {
    n = v.item(i); a = n.getAttribute('class') || '';
    n.setAttribute('title', 'Move code');
    if (n.firstChild && n.firstChild.nodeName == 'CODE') {
      if (a.indexOf('prettyprint') === -1 &&
          (n.firstChild.getAttribute('class') || '').indexOf('prettyprint') === -1) {
        //console.log(n.firstChild.getAttribute('class'))
        n.setAttribute('class', a+' prettyprint lang-mv');
      }
    }
  }
  v = document.getElementsByTagName('code');
  for (i=0,L=v.length;i<L;++i) {
    n = v.item(i); a = n.getAttribute('class') || '';
    if (a.indexOf('prettyprint') === -1)
      n.setAttribute('class', a+' prettyprint lang-mv');
  }
  $('section.body samp').attr('title', 'Output');
  var script = document.createElement('script');
  script.async = true;
  script.src = "res/prettify.js";
  document.getElementsByTagName('head')[0].appendChild(script);
});

// Load custom fonts gracefully
(function (){
function waitForStyles() {
  for (var i = 0; i < document.styleSheets.length; i++)
    if (/googleapis/.test(document.styleSheets[i].href))
      return document.body.className += " custom-fonts-loaded";
  setTimeout(waitForStyles, 100);
}
setTimeout(function() {
  if (/AppleWebKit/.test(navigator.userAgent) && /iP[oa]d|iPhone/.test(navigator.userAgent)) return;
  var link = document.createElement("LINK");
  link.type = "text/css";
  link.rel = "stylesheet";
  link.href = "http://fonts.googleapis.com/css?family=Droid+Serif:regular,italic,bold|Droid+Sans+Mono";
  document.documentElement.getElementsByTagName("HEAD")[0].appendChild(link);
  waitForStyles();
}, 20);
})();

    </script>
  </body>
</html>
