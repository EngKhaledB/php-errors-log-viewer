<?php

define( 'ERROR_LOG_FILE_PATH', '../../error.log' );

function get_logs_rows() {

	$log       = file_get_contents( ERROR_LOG_FILE_PATH );
	$log       = preg_replace( '/(\d{4}\/\d{2}\/\d{2})/', 'RANDOM_SPLITTER_TEXT_FOR_PREG_REPLACE_USE123456$1', $log );
	$log_lines = explode( 'RANDOM_SPLITTER_TEXT_FOR_PREG_REPLACE_USE123456', $log );

	$log_lines = array_map( function ( $item ) {
		if ( empty( $item ) ) {
			return null;
		}

		return "<div class='log_item'><pre>$item</pre></div>";
	}, $log_lines );


	return array_reverse( $log_lines );
}

if ( isset( $_SERVER['HTTP_IS_FETCH_REQUEST'] ) ) {
	header( 'Content-Type: application/json; charset=utf-8' );

	echo json_encode( get_logs_rows() );
	die;
}

?>
<style>
    .log_item {
        margin: 5px;
        padding: 10px;

        background: #f1f1f1;
    }


    pre {
        display: inline-block;
        max-width: 100%;
        white-space: pre-wrap;
        line-height: 20px;
    }
</style>

<script src="https://unpkg.com/vue@3"></script>

<div id="app">
    <h1>PHP Error Viewer with auto-refresh</h1>
    <h2 v-if="fetching">Fetching ..</h2>
    <div v-else v-for="log in logs" v-html="log"></div>
</div>

<script>
    Vue.createApp({
        data() {
            return {
                logs: [],
                fetching: true
            }
        },
        mounted() {
            this.fetchLogs();

            setInterval(_ => {
                this.fetchLogs();
            }, 2000);
        },
        methods: {
            fetchLogs() {
                fetch(window.location.href, {
                    headers: {
                        'is-fetch-request': 'fetch'
                    }
                }).then(res => res.json()).then(json => {
                    this.logs = json;
                    this.fetching = false;
                });
            }
        }
    }).mount('#app')
</script>

