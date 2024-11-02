<?php
    class SearchApiRequest extends EngineRequest {
        private $api_key = $this->opts->searchapi_apikey;  // API key

        public function get_request_url() {
            // Base URL for the SearchAPI request
            $url = "https://www.searchapi.io/api/v1/search";
            
            // Set up query parameters for the request
            $params = array(
                "engine" => "google",
                "q" => $this->query,
                "api_key" => $this->api_key
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