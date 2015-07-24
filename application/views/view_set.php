<?php PagerWidget::header(); ?>
<h2>
	<?= format_html($key)?>
	<?php if ($exists) { ?>
	<a href="<?= manager_site_url('rename', 'index', 'key=' . urlencode($key)); ?>">
		<img src="<?= base_url('static/images/edit.png'); ?>" width="16" height="16" title="重命名" alt="重命名" /></a>
	<a href="<?= manager_site_url('delete', 'index', 'key=' . urlencode($key)); ?>" class="delkey">
		<img src="<?= base_url('static/images/delete.png'); ?>" width="16" height="16" title="删除" alt="删除" /></a>
	<a href="<?= manager_site_url('export', 'index', 'key=' . urlencode($key)); ?>">
		<img src="<?= base_url('static/images/export.png'); ?>" width="16" height="16" title="导出" alt="导出" /></a>
	<?php } ?>
</h2>
<?php 
if ( ! $exists ) {
?>
此key不存在。
<?php 
	die();
} 
?>
<table>
	<tr>
		<td><div>类型(Type):</div></td>
		<td><div>
				<?= format_html($type)?>
			</div></td>
	</tr>
	<tr>
		<td><div><abbr title="键的生存期">生存期(TTL)</abbr>:</div></td>
		<td>
			<div>
				<?= ($ttl == -1) ? '永不过期' : $ttl . ' 秒';?>
				<a href="<?= manager_site_url('ttl', 'index', 'key=' . urlencode($key)); ?>">
					<img src="<?= base_url('static/images/edit.png'); ?>" width="16" height="16" title="修改生存期" alt="修改生存期" class="imgbut" />
				</a>
			</div>
		</td>
	</tr>
	<tr>
		<td><div>编码类型(Encoding):</div></td>
		<td>
			<div>
				<?= format_html($encoding)?>
			</div>
		</td>
	</tr>
	<tr>
		<td><div>大小(Size):</div></td>
		<td>
			<div>
				<?= $size?>
				<?= ($type == 'string') ? '字符' : '项'?>
			</div>
		</td>
	</tr>
</table>
<br />
<table>
	<tr>
		<th><div>Value</div></th>
		<th><div>&nbsp;</div></th>
		<th><div>&nbsp;</div></th>
	</tr>
	<?php 
		$alt = 0;
		foreach ($values as $value) {
	?>
	<tr <?= $alt ? 'class="alt"' : ''?>>
		<td>
			<div>
				<?= nl2br(format_html($value)); ?>
			</div>
		</td>
		<td>
			<div> 
				<a href="<?= manager_site_url('edit', 'index', 'key=' . urlencode($key) . '&type=' . $type . '&value=' .  $value); ?>">
					<img src="<?= base_url('static/images/edit.png'); ?>" width="16" height="16" title="编辑" alt="编辑" />
				</a> 
			</div>
		</td>
		<td>
			<div> 
				<a href="<?= manager_site_url('delete', 'index', 'key=' . urlencode($key) . '&type=' . $type . '&value=' .  $value); ?>" class="delval">
					<img src="<?= base_url('static/images/delete.png'); ?>" width="16" height="16" title="删除" alt="删除" />
				</a> 
			</div>
		</td>
	</tr>
	<?php 
	$alt = ! $alt;
}
?>
</table>
<p> <a href="<?= manager_site_url('edit', 'index', 'key=' . urlencode($key) . '&type=' . $type ); ?>" class="add">新增一个值</a> </p>

<?php PagerWidget::footer(); ?>
