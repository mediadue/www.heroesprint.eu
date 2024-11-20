	<section id="contentArea" class="content-area container  spacing-normal clearfix">
      <div class="row">
        <h1 class="txt-display">FAQ</h1>
        <div class="col-sm-12 content">
          <div class="panel-group" id="accordion">
              <?php
              $faq=getTable("faq","Ordinamento ASC","attivo=1");
							$k=0;
              while (list($key, $row) = each($faq)) { 
              	$k++;
              	?>
								<div class="panel panel-info">
	                <div class="panel-heading">
	                <h4 class="panel-title"><a class="accordion-toggle collapsed" href="#pannello-<?php echo $k; ?>" data-toggle="collapse" data-parent="#accordion"><?php echo ln($row["domanda"]); ?></a></h4>
	                </div>
	                <div class="panel-collapse collapse <?php if($k==1) echo "in"; ?>" id="pannello-<?php echo $k; ?>">
	                	<div class="panel-body"><?php echo ln($row["risposta"]); ?></div>
	                </div>
	              </div>	
							<? } ?>
            </div>
        </div>

      </div>
    </section><!-- /#ocontentArea -->