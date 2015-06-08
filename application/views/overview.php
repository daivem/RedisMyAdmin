<?php PagerWidget::header(); ?>
<?php foreach ($server_list as $i => $server) { ?>
<div class="server">
	<h2><?= isset($server['name']) ? $server['name'] : format_html($server['host'])?></h2>
	<table>
		<?php 
			if ($info[$i]['error']) {
		?>
		<tr>
			<td colspan="2"><div><span style="color:#F00; font-weight:bold;">无法连接<?= $server['host'] . ':' . $server['port']; ?></span></div></td>
		</tr>
		<tr>
			<td><div>服务端版本：</div></td>
			<td><div>&nbsp;</div></td>
		</tr>
		<tr>
			<td><div>Keys数量：</div></td>
			<td><div>&nbsp;</div></td>
		</tr>
		<tr>
			<td><div>内存使用量：</div></td>
			<td><div>&nbsp;</div></td>
		</tr>
		<tr>
			<td><div>运行时间：</div></td>
			<td><div>&nbsp;</div></td>
		</tr>
		<tr>
			<td><div>上次保存：</div></td>
			<td><div>&nbsp;</div></td>
		</tr>
		<?php
			} elseif ( isset($info[$i]['cluster_list']) ) {
		?>
		<tr>
			<td><div>集群服务器：</div></td>
			<td><div><?= implode('<br />', $info[$i]['cluster_list']) ?></div></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><a href="<?= manager_site_url('overview', 'index', array('server_id' => $i)) ?>">查看</a></td>
		</tr>
		<?php 
			} else {
		?>
		<tr>
			<td><div>服务器地址：</div></td>
			<td><div><?= $server['host'] . ':' . $server['port']; ?></div></td>
		</tr>
		<tr>
			<td><div>服务端版本：</div></td>
			<td><div><?= $info[$i]['redis_version']?></div></td>
		</tr>
		<tr>
			<td><div>Keys数量：</div></td>
			<td><div><?= $info[$i]['size']?></div></td>
		</tr>
		<tr>
			<td><div>内存使用量：</div></td>
			<td><div><?= format_size($info[$i]['used_memory'])?></div></td>
		</tr>
		<tr>
			<td><div>运行时间：</div></td>
			<td><div><?= format_ago($info[$i]['uptime_in_seconds'])?></div></td>
		</tr>
		<tr>
			<td><div>上次保存：</div></td>
			<?php
				$last_save_time = NULL;
				if ( isset($info[$i]['rdb_last_save_time']) ) {
					$last_save_time = $info[$i]['rdb_last_save_time'];
				} elseif ( isset($info[$i]['last_save_time']) ) {
					$last_save_time = $info[$i]['last_save_time'];
				}
			?>
			<td>
				<div><?= ( $last_save_time === NULL ? '未知' : format_ago(time() - $last_save_time, true) ) ?> 
					<a href="<?= manager_site_url('save', 'index'); ?>">
						<img src="<?= base_url('static/images/save.png'); ?>" width="16" height="16" title="立刻保存" alt="立刻保存" class="imgbut"  />
					</a>
				</div>
			</td>
		</tr>
		<?php 
			}
		?>
	</table>
</div>
<?php } ?>

<?php PagerWidget::footer(); ?>
