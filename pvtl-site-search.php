<?php
/**
 * Plugin Name: PVTL Site Search
 * Description: Admin-only content search tool to find pages containing specific terms
 * Version: 1.0.0
 * Author: Pivotal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class PVTL_Site_Search {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    public function add_admin_menu() {
        add_menu_page(
            'Site Search',
            'Site Search',
            'manage_options',
            'pvtl-site-search',
            array($this, 'render_search_page'),
            'dashicons-search',
            30
        );
    }
    
    public function render_search_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        echo '<div class="wrap">';
        echo '<h1>Content Search Tool</h1>';
        
        // Search form
        echo '<form method="get" style="margin: 20px 0;">';
        echo '<input type="hidden" name="page" value="pvtl-site-search">';
        echo '<label for="search"><strong>Search for:</strong></label><br>';
        echo '<input type="text" name="search" id="search" style="width: 300px; padding: 5px; margin: 10px 0;" placeholder="Enter search term..." value="' . esc_attr($search_term) . '">';
        echo '<button type="submit" class="button button-primary" style="padding: 5px 15px;">Search</button>';
        echo '</form>';
        
        if (empty($search_term)) {
            echo '<p>Please provide a search term to find pages containing that content.</p>';
            echo '</div>';
            return;
        }
        
        echo '<hr>';
        echo '<h2>Searching for: "' . esc_html($search_term) . '"</h2>';
        
        // Get all published posts, pages, and custom post types
        $args = array(
            'post_type' => 'any',
            'post_status' => 'publish',
            'posts_per_page' => -1
        );
        
        $posts = get_posts($args);
        $found_pages = array();
        
        echo '<p>Searching through ' . count($posts) . ' posts...</p>';
        
        foreach ($posts as $post) {
            $permalink = get_permalink($post->ID);
            $post_content = $post->post_content;
            $post_title = $post->post_title;
            
            // Check if content contains the search term (case insensitive)
            if (stripos($post_content, $search_term) !== false) {
                // Find all occurrences and extract context
                $excerpts = array();
                $content_lower = strtolower($post_content);
                $search_lower = strtolower($search_term);
                $offset = 0;
                
                while (($pos = strpos($content_lower, $search_lower, $offset)) !== false) {
                    // Extract 150 characters before and after the match
                    $start = max(0, $pos - 150);
                    $length = 300 + strlen($search_term);
                    $excerpt = substr($post_content, $start, $length);
                    
                    // Clean up HTML tags for display
                    $excerpt = strip_tags($excerpt);
                    $excerpt = trim($excerpt);
                    
                    // Add ellipsis if needed
                    if ($start > 0) {
                        $excerpt = '...' . $excerpt;
                    }
                    if ($start + $length < strlen($post_content)) {
                        $excerpt = $excerpt . '...';
                    }
                    
                    // Highlight the search term
                    $excerpt = preg_replace('/(' . preg_quote($search_term, '/') . ')/i', '<strong style="background-color: yellow;">$1</strong>', $excerpt);
                    
                    $excerpts[] = $excerpt;
                    $offset = $pos + strlen($search_term);
                }
                
                $found_pages[] = array(
                    'url' => $permalink,
                    'title' => $post_title,
                    'type' => $post->post_type,
                    'id' => $post->ID,
                    'excerpts' => $excerpts,
                    'count' => count($excerpts)
                );
            }
        }
        
        // Display results
        if (!empty($found_pages)) {
            echo '<h2>Found ' . count($found_pages) . ' pages with "' . esc_html($search_term) . '":</h2>';
            
            foreach ($found_pages as $item) {
                echo '<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">';
                echo '<h3>' . esc_html($item['title']) . '</h3>';
                echo '<p><strong>URL:</strong> <a href="' . esc_url($item['url']) . '" target="_blank">' . esc_html($item['url']) . '</a></p>';
                echo '<p><strong>Type:</strong> ' . esc_html($item['type']) . ' | <strong>ID:</strong> ' . $item['id'] . ' | <strong>Occurrences:</strong> ' . $item['count'] . '</p>';
                
                echo '<div style="background-color: white; padding: 10px; margin-top: 10px;">';
                echo '<strong>Content excerpts:</strong>';
                foreach ($item['excerpts'] as $index => $excerpt) {
                    echo '<p style="margin: 10px 0; padding: 10px; border-left: 3px solid #007cba;">';
                    echo '<em>Occurrence ' . ($index + 1) . ':</em><br>';
                    echo $excerpt;
                    echo '</p>';
                }
                echo '</div>';
                echo '</div>';
            }
            
            // Plain URL list
            echo '<hr>';
            echo '<h3>Plain URL List (for easy copying):</h3>';
            echo '<textarea style="width: 100%; height: 150px;">';
            foreach ($found_pages as $item) {
                echo esc_html($item['url']) . "\n";
            }
            echo '</textarea>';
            
        } else {
            echo '<p style="color: red;">No pages containing "' . esc_html($search_term) . '" were found.</p>';
        }
        
        echo '<hr>';
        echo '<p><em>Search completed at ' . date('Y-m-d H:i:s') . '</em></p>';
        echo '</div>';
    }
}

// Initialize the plugin
new PVTL_Site_Search();
