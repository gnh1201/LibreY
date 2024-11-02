<?php
    class SearchApiRequest extends EngineRequest {

        public function get_request_url() {
            // API key
            $api_key = $this->opts->searchapi_apikey;
            
            // Base URL for the SearchAPI request
            $query_encoded = str_replace("%22", "\"", urlencode($this->query));
            $url = "https://www.searchapi.io/api/v1/search";
            
            // Set up query parameters for the request
            $params = array(
                "engine" => "google",
                "q" => $query_encoded,
                "api_key" => $api_key
            );

            // Concatenate URL with query parameters
            return $url . '?' . http_build_query($params);
        }

        public function parse_results($response) {
            $results = array();
            $response_data = json_decode($response, true);  // Decode JSON response into an array

            // Check if 'organic_results' exists in the response
            if (isset($response_data['organic_results'])) {
                foreach ($response_data['organic_results'] as $result) {
                    $title = $result['title'] ?? "No title";
                    $url = $result['link'] ?? "No URL";
                    $description = $result['snippet'] ?? "No description";

                    // Add parsed result to the results array
                    array_push($results, array(
                        "title" => htmlspecialchars($title),
                        "url" => htmlspecialchars($url),
                        "description" => htmlspecialchars($description)
                    ));
                }
            } else {
                // Handle case when no results are available or request failed
                $results["error"] = array(
                    "message" => "Failed to fetch results or no results available."
                );
            }

            return $results;
        }
    }
?>