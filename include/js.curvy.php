<?php $i_curvy=1 ?>

<script type="text/javascript" src="js/curvy/curvycorners.js"></script>

<script type="text/javascript">

  function ccurvy()
  {
      /*
      The new 'validTags' setting is optional and allows
      you to specify other HTML elements that curvyCorners
      can attempt to round.

      The value is comma separated list of html elements
      in lowercase.

      validTags: ["div", "form"]

      The above example would enable curvyCorners on FORM elements.
      */
      settings = {
          tl: { radius: 10 },
          tr: { radius: 10 },
          bl: { radius: 10 },
          br: { radius: 10 },
          antiAlias: true,
          autoPad: true,
          validTags: ["div"]
      }
      
      settings2 = {
          tl: { radius: 20 },
          tr: { radius: 10 },
          bl: { radius: 0 },
          br: { radius: 0 },
          antiAlias: true,
          autoPad: true,
          validTags: ["div"]
      }
      
      settings3 = {
          tl: { radius: 0 },
          tr: { radius: 0 },
          bl: { radius: 10 },
          br: { radius: 10 },
          antiAlias: true,
          autoPad: true,
          validTags: ["div"]
      }
      
      settings4 = {
          tl: { radius: 0 },
          tr: { radius: 0 },
          bl: { radius: 20 },
          br: { radius: 20 },
          antiAlias: true,
          autoPad: true,
          validTags: ["div"]
      }

      /*
      Usage:

      newCornersObj = new curvyCorners(settingsObj, classNameStr);
      newCornersObj = new curvyCorners(settingsObj, divObj1[, divObj2[, divObj3[, . . . [, divObjN]]]]);
      */
      //var myBoxObject = new curvyCorners(settings, "roundedBox");
      var myBoxObject2 = new curvyCorners(settings2, "roundedBox2");
      var myBoxObject3 = new curvyCorners(settings3, "roundedBox3");
      var myBoxObject4 = new curvyCorners(settings4, "roundedBox4");
      
      //myBoxObject.applyCornersToAll();
      myBoxObject2.applyCornersToAll();
      myBoxObject3.applyCornersToAll();
      myBoxObject4.applyCornersToAll();

  }

</script>
