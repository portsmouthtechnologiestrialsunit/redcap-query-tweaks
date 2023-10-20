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

        if (!isset($_GET['pid'])) return;

        $settings = $this->getProjectSettings();

        global $user_rights, $data_resolution_enabled, $lang;

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

            switch (PAGE) {
                case "index.php":
                    // if (!settings['dq-table']) break;
                    $dt_title = '<div style="padding: 0;"><i class="fas fa-comments"></i> ' . $lang['dataqueries_244'] . '</div>';
                    $dt = '<div class="col-12 col-md-2 float-right">';
                    $dt_col_widths_headers = array(
                        array(140, '', 'left'),
                        array(80,  '', 'center')
                    );
                    $dt_row_data = array();
                    $order = array('OPEN', 'OPEN_UNRESPONDED', 'OPEN_RESPONDED', 'CLOSED', 'VERIFIED', 'DEVERIFIED');
                    foreach ($order as $ord) {
                        array_push($dt_row_data,
                            array(
                                ucwords(strtolower(str_replace('_', ' - ', $ord))),
                                $queryStatuses[$ord]
                            )
                        );
                    }
                    $dt .= '<div>' .  renderGrid('dq-table', $dt_title, 220, 'auto', $dt_col_widths_headers, $dt_row_data, false) . '</div>';

                    ?> <script type="text/javascript">
                        $(document).ready(function() {
                            $('#dq-table').appendTo($('div.float-right.text-left'));
                        })
                    </script>
                    <?php
                    break;
            }
        }
    }

}