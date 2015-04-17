<div class="preview">
	<div class="preview-container">
		<div class="preview-div">
			<iframe src="/mobile/home?appid=<?php echo $app->id_app;?>" frameBorder="0" width="300" scrolling="no" height="533"></iframe>
		</div>
		<div class="mobile-two-code">
			<span><a href="/market/app?appid=<?php echo $app->id_app;?>"><?php echo lang('market_home_checktheappdetails');?></a></span>
			<img src="<?php echo $app->mobilewebsite2dcode;?>" >
			<span><?php echo lang('market_app_mobilescan');?><?php echo WEBSITE_URL."/mobile/home?appid=".$app->id_app;?></span>
		</div>
	</div>
	<div class="app-recommend">
		<div class="week-rank">
			<div class="rank-tit">
				<span class="tit"><?php echo lang('market_app_recommendeddownload');?></span>
				<div class="clearboth"></div>
			</div>
			<ul class="rank-tab-body rank-body">
				<?php foreach($recommendapps as $key=>$a):?>
				<li>
					<span class="rank-num rank-num<?php echo ($key+1);?>"><?php echo ($key+1);?></span>
					<div class="rank-info">
						<a href="/market/app?appid=<?php echo $a->id_app;?>" title="<?php echo $a->name_app;?>" class="name" target="_blank"><?php echo $a->name_app;?></a>
						<div class="down-count">
							<span><?php echo $a->download_time_app;?><?php echo lang('market_home_times');?></span><?php echo lang('market_home_download');?>
						</div>
					</div>
					<img src="<?php echo $a->icon_app;?>" alt="<?php echo $a->name_app;?>"></a>
					<div class="clearboth"></div>
				</li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
	<div class="clearboth"></div>
</div>