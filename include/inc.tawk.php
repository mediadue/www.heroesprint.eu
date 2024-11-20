<?php 
if($clName=="it") {
  $tawkAPI="576795aeec8ca2561a0715fd";
} else {
  $tawkAPI="5767a3e9888093210eadebdf"; 
}

if (!_bot_detected()) { ?>
  <!--Start of Tawk.to Script-->
  var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
  (function(){
  var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
  s1.async=true;
  s1.src='https://embed.tawk.to/<?php echo $tawkAPI; ?>/default'; 
  //s1.src='<?php echo $objUtility->getPathRoot(); ?>js/tawk_<?php echo $tawkAPI; ?>.js.php';
  s1.charset='UTF-8';
  s1.setAttribute('crossorigin','*');
  s0.parentNode.insertBefore(s1,s0);
  })();
  <!--End of Tawk.to Script-->
<? } ?> 