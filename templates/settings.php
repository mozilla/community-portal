<h1>Mozilla Theme Settings</h1>
<form method="POST" action="/wp-admin/admin.php?page=theme-panel">
    <?php print wp_nonce_field('protect_content', 'admin_nonce_field'); ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="google-analytics-id">Google Analytics ID</label>
                </th>
                <td>
                    <input type="text" id="google-analytics-id" name="google_analytics_id" class="regular-text" value="<?php print $options['google_analytics_id']; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="google-analytics-id">Default Open Graph Title</label>
                </th>
                <td>
                    <input type="text" id="default-open-graph-title" name="default_open_graph_title" class="regular-text" value="<?php print $options['default_open_graph_title']; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="google-analytics-id">Default Open Graph Description</label>
                </th>
                <td>
                    <input type="text" id="default-open-graph-desc" name="default_open_graph_desc" class="regular-text" value="<?php print $options['default_open_graph_desc']; ?>" />
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Save Settings" />
</form>
