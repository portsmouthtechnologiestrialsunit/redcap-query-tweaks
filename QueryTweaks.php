<?php
namespace PTTU\QueryTweaks;
error_reporting(E_ALL);

use ExternalModules\AbstractExternalModule;
use DataQuality;
use RCView;

class QueryTweaks extends AbstractExternalModule {

    public function redcap_every_page_top($project_id) {

        /*
        Query Badge:
        Get the number of open queries and append the number of open queries to Resolve Issues in the app panel
        */

        $settings = $this->getProjectSettings();

        if (!isset($_GET['pid'])) return;

        global $user_rights, $data_resolution_enabled;

        if ($data_resolution_enabled == '2' && $user_rights['data_quality_resolution'] > 0) {
            
			// Get a count of unresolved issues
            $dq = new DataQuality();
			$queryStatuses = $dq->countDataResIssues();
			$numOpenIssues = $queryStatuses['OPEN'];
            
            if ($settings['badges']) {
                if ($numOpenIssues > 0) {
                    ?> <script type="text/javascript">
                        $(document).ready(function() {
                            $('<span/>', {
                                id: 'dq_issue_count',
                                class: 'badgerc',
                                text: <?php echo $numOpenIssues; ?>
                            }).appendTo($('#app_panel a:contains("Resolve Issues")'));
                        });
                    </script>
                    <?php
                }
            }
        }
    }

}