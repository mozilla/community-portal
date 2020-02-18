<h1>Mozilla Theme Settings</h1>
<form method="POST" action="/wp-admin/admin.php?page=theme-panel">
    <?php print wp_nonce_field('protect_content', 'admin_nonce_field'); ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="report-email">Report Group / Event Email</label>
                </th>
                <td>
                    <input type="text" id="report-email" name="report_email" class="regular-text" value="<?php print isset($options['report_email']) ? $options['report_email'] : ''; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="github-link">Github Link</label>
                </th>
                <td>
                    <input type="text" id="github-link" name="github_link" class="regular-text" value="<?php print $options['github_link']; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="community-discourse">Community Portal Discourse</label>
                </th>
                <td>
                    <input type="text" id="community-discourse" name="community_discourse" class="regular-text" value="<?php print $options['community_discourse']; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="mailchimp">Mailchimp API Key</label>
                </th>
                <td>
                    <input type="text" id="mailchimp" name="mailchimp" class="regular-text" value="<?php print $options['mailchimp']; ?>" />
                </td>
            </tr>
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
                    <label for="error-404-copy">Discourse API Key</label>
                </th>
                <td>
                    <input type="text" id="discourse-api-key" name="discourse_api_key" class="regular-text" value="<?php print isset($options['discourse_api_key']) ? $options['discourse_api_key'] : ''; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="error-404-copy">Discourse API URL</label>
                </th>
                <td>
                    <input type="text" id="discourse-api-url" name="discourse_api_url" class="regular-text" value="<?php print isset($options['discourse_api_url']) ? $options['discourse_api_url'] : ''; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="error-404-copy">Discourse URL</label>
                </th>
                <td>
                    <input type="text" id="discourse-url" name="discourse_url" class="regular-text" value="<?php print isset($options['discourse_url']) ? $options['discourse_url'] : ''; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="error-404-copy">Mapbox Access Token</label>
                </th>
                <td>
                    <input type="text" id="mapbox" name="mapbox" class="regular-text" value="<?php print isset($options['mapbox']) ? $options['mapbox'] : ''; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="default-open-graph-title">Default Open Graph Title</label>
                </th>
                <td>
                    <input type="text" id="default-open-graph-title" name="default_open_graph_title" class="regular-text" value="<?php print $options['default_open_graph_title']; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="default-open-graph-desc">Default Open Graph Description</label>
                </th>
                <td>
                    <input type="text" id="default-open-graph-desc" name="default_open_graph_desc" class="regular-text" value="<?php print $options['default_open_graph_desc']; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="image-max-filesize">Max Image Filesize Upload (KB)</label>
                </th>
                <td>
                    <input type="text" id="image-max-filesize" name="image_max_filesize" class="regular-text" value="<?php print isset($options['image_max_filesize']) ? $options['image_max_filesize'] : 500; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="error-404-title">404 Error Title</label>
                </th>
                <td>
                    <input type="text" id="error-404-title" name="error_404_title" class="regular-text" value="<?php print isset($options['error_404_title']) ? $options['error_404_title'] : ''; ?>" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="error-404-copy">404 Error Copy</label>
                </th>
                <td>
                    <input type="text" id="error-404-copy" name="error_404_copy" class="regular-text" value="<?php print isset($options['error_404_copy']) ? $options['error_404_copy'] : ''; ?>" />
                </td>
            </tr>
        </tbody>
    </table>
    <input type="submit" value="Save Settings" />
</form>
