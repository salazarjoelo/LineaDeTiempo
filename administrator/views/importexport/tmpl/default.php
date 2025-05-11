<div class="csv-mapping-interface">
    <div class="column-list" data-sortable>
        <?php foreach ($this->csvHeaders as $header) : ?>
            <div class="column-item" data-column="<?php echo $header; ?>">
                <?php echo $header; ?>
                <select name="mapping[<?php echo $header; ?>]">
                    <option value=""><?php echo Text::_('COM_LINEADETIEMPO_IGNORE_COLUMN'); ?></option>
                    <?php foreach ($this->dbFields as $field) : ?>
                        <option value="<?php echo $field; ?>"><?php echo $field; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>
    </div>
</div>