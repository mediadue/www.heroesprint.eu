<?php
function packerligne($longitude,$latitude,$description)
{
  return chr(0x02).pack("V",strlen($description)+14).pack("V",round($longitude*100000)).pack("V",round($latitude*100000)).$description.chr(0x00);
}
 	
function db2ov2($nomfichier,$x,$y,$desc) {
	$fichier=fopen($nomfichier, 'wb+');
	fwrite($fichier,packerligne($x,$y,$desc));
	fclose ($fichier);
}

$prs=location();
if(is_array($prs)) { ?> 
  <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=<?php echo $prs[0]['API_Key']; ?>&sensor=true_OR_false"></script>
  <script type="text/javascript">
  
  function GLoad() { 
    <?php
    $minX=0;
    $minY=0;
    $maxX=0;
    $maxY=0;
    while (list($key, $row) = each($prs)) {
      $coords=$row['Url'];
      $tkey=$row['API_Key'];
      $or_name=onlyreadables($row['nome_della_mappa']);
      
      $dataxy = explode ("&ll=",$coords);    
      $dataxyarr= explode (",",$dataxy[1]);     
      $y = $dataxyarr[0];
      $dataxarr= explode ("&",$dataxyarr[1]);    
      $x =  $dataxarr[0];     
      $dataz = explode ("z=",$coords);     
      $zarr= explode ("&",$dataz[1]);    
      $z=$zarr[0];
      
      if($x<$minX || $minX==0) $minX=$x;
      if($x>$maxX || $maxX==0) $maxX=$x;
      
      if($y<$minY || $minY==0) $minY=$y;
      if($y>$maxY || $maxY==0) $maxY=$y;
    }
    
    $area=(abs($maxX-$minX)*abs($maxY-$minY));
    $zoom=floor(($area*2)/848.59);

    if($zoom==0) $zoom=13;
    if($zoom<0) $zoom=0;

    if($coords!=""){ ?>
      var mygmap=gmap_load("<?php echo (($minX+$maxX)/2); ?>","<?php echo (($minY+$maxY)/2); ?>","<?php echo $or_name; ?>",<?php echo $zoom; ?>);
    <? } 
    
    reset($prs);
    while (list($key, $row) = each($prs)) {
      $coords=$row['Url'];
      $tkey=$row['API_Key'];
      $or_name=onlyreadables($row['nome_della_mappa']);
      
       
      
      $dataxy = explode ("&ll=",$coords);    
      $dataxyarr= explode (",",$dataxy[1]);     
      $y = $dataxyarr[0];
      $dataxarr= explode ("&",$dataxyarr[1]);    
      $x =  $dataxarr[0];     
      $dataz = explode ("z=",$coords);     
      $zarr= explode ("&",$dataz[1]);    
      $z=$zarr[0]; 
      
      if($coords!=""){ ?>
        gmap_loadMarker(mygmap,"<?php echo $x; ?>","<?php echo $y; ?>","<?php echo gmapInfo($row['id']); ?>");
      <? } ?>
    <? } ?>  
  }
  </script>
<? } ?>	