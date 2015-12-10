<?php PagerWidget::header(); ?>
<h2>
	<?= $title ?>
</h2>
<form action="" method="post">
	<p>
		<label for="type">类型(Type):</label>
		<select name="type" id="type">
			<option value="string" <?= ($type == 'string') ? 'selected="selected"' : ''?>>String</option>
			<option value="hash"   <?= ($type == 'hash'  ) ? 'selected="selected"' : ''?>>Hash</option>
			<option value="list"   <?= ($type == 'list'  ) ? 'selected="selected"' : ''?>>List</option>
			<option value="set"    <?= ($type == 'set'   ) ? 'selected="selected"' : ''?>>Set</option>
			<option value="zset"   <?= ($type == 'zset'  ) ? 'selected="selected"' : ''?>>ZSet</option>
		</select>
	</p>
	<p>
		<label for="key">Key:</label>
		<input type="text" name="key" id="key" size="80" maxlength="250" value="<?= format_html($key); ?>">
	</p>
	<p id="hkeyp">
		<label for="khey">Hash key:</label>
		<input type="text" name="hkey" id="hkey" size="80" maxlength="250" <?= isset($GETS['hkey']) ? 'value="'.format_html($GETS['hkey']).'"' : ''; ?>>
	</p>
	<p id="indexp">
		<label for="index">Index:</label>
		<input type="text" name="index" id="index" size="30" <?= isset($GETS['index']) ? 'value="'.format_html($GETS['index']).'"' : ''; ?>>
		<span class="info">留空则插入到尾部，-1则插入到表头，输入已存在的index则替换此index的值</span> </p>
	<p id="scorep">
		<label for="score">Score:</label>
		<input type="text" name="score" id="score" size="80" <?= isset($GETS['score']) ? 'value="'.format_html($GETS['score']).'"' : ''; ?>>
		<input type="hidden" name="oldscore" id="oldscore" size="80" value="<?= isset($GETS['score']) ? format_html($GETS['score']) : ''; ?>">
	</p>
	<p>
		<label for="value">Value:</label>
		<textarea name="value" id="value" cols="80" rows="20"><?= nl2br(format_html($value))?></textarea>
	</p>
	<input type="hidden" name="oldvalue" value="<?= format_html($value)?>">
    <?php 
            if ( ($type == 'string') && ($is_edit) ) {
    ?>
    <p><input type="checkbox" name="keep_ttl" value="1" checked>保持生存期(ttl)不变</p>
    <?php
        }
    ?>
	<p>

		<input type="submit" class="button" value="<?= $is_edit ? '编辑' : '新增'?>">
	</p>
</form>

<?php PagerWidget::footer(); ?>
