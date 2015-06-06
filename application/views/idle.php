<?php PagerWidget::header(); ?>
<h2>
<?= $title ?>
</h2>
<form action="<?= manager_site_url($c, 'build_report'); ?>" method="post">
	<div>
	当前db的Key总数为：<?= $db_size ?>
	<?php if ( $db_size >= 100000 ) { ?>
	数据较多，生成报表会较久
	<?php } ?>
	</div>
	<div>
	<?php
		if ( $idle_infos ) {
	?>
		当前空闲列表生成于<?= date('Y-m-d H:i:s', $idle_infos['timestamp']) ?>
	<?php } else { ?>
		没有找到历史生成的空闲列表
	<?php } ?>
		
	</div>
	<div>
			<lable>数量：</lable>
			<select name="cnt">
				<option value="10">10</option>
				<option value="50">50</option>
				<option value="100">100</option>
				<option value="500">500</option>
				<option value="1000">1000</option>
			</select>
		<input type="submit" value="重新生成">
	</div>
</form>
<?php
if ( $idle_infos ) {
?>
<br />
<p><span style="font-weight:bold; color:#00F">注：使用[查看]等操作将会将key的空闲时间重置</span></p>
<table>
	<tr>
		<th>Index</th>
		<th>Key</th>
		<th>空闲时间(秒)</th>
		<th>操作</th>
	</tr>
<?php 
		$alt = 0;
		foreach ($idle_infos['idle_list'] as $key => $arr) {
	?>
	<tr <?= $alt ? 'class="alt"' : ''?>>
		<td>
			<div><?= $key ?></div>
		</td>
		<td>
			<div><?= nl2br(format_html($arr['key'])); ?></div>
		</td>
		<td>
			<div><?= nl2br(format_html($arr['idle_time'])); ?></div>
		</td>
		<td>
			<div> 
				<a href="<?= manager_site_url('view', 'index', 'key=' . urlencode($arr['key'])) ?>">查看</a> 
			</div>
		</td>
	</tr>
	<?php 
		$alt = ! $alt;
	}
	?>
</table>
<?php
}
?><?php PagerWidget::footer(); ?>
