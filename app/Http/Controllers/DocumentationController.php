<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DocumentationController extends Controller
{
    private $docsPath;
    private $docs;

    public function __construct()
    {
        $this->docsPath = base_path();

        // List semua dokumentasi yang tersedia
        // List semua dokumentasi yang tersedia
        $this->docs = [
            'readme' => [
                'title' => 'Introduction (Read Me)',
                'file' => 'README.md',
                'icon' => '👋',
                'category' => 'Getting Started'
            ],
            'frontend' => [
                'title' => 'Frontend Guide (Vue.js)',
                'file' => 'FRONTEND.md',
                'icon' => '🖥️',
                'category' => 'Getting Started'
            ],
            'installation' => [
                'title' => 'Installation Guide',
                'file' => 'INSTALLATION.md',
                'icon' => '📥',
                'category' => 'Getting Started'
            ],
            'guide' => [
                'title' => 'Quick Start Guide',
                'file' => 'GUIDE.md',
                'icon' => '🎯',
                'category' => 'Getting Started'
            ],
            'validation-flow' => [
                'title' => 'Validation Flow',
                'file' => 'VALIDATION-FLOW.md',
                'icon' => '🔍',
                'category' => 'Getting Started'
            ],
            'quick-commands' => [
                'title' => 'Quick Commands',
                'file' => 'QUICK-COMMANDS.md',
                'icon' => '⌨️',
                'category' => 'Getting Started'
            ],
            'structure' => [
                'title' => 'File Structure',
                'file' => 'STRUCTURE.md',
                'icon' => '📂',
                'category' => 'Architecture'
            ],
            'middleware' => [
                'title' => 'Middleware & Permissions',
                'file' => 'MIDDLEWARE.md',
                'icon' => '🔐',
                'category' => 'Security'
            ],
            'roles-and-permissions' => [
                'title' => 'Roles & Permissions',
                'file' => 'ROLES-AND-PERMISSIONS.md',
                'icon' => '👥',
                'category' => 'Security'
            ],
            'rate-limiting' => [
                'title' => 'Rate Limiting',
                'file' => 'RATE-LIMITING.md',
                'icon' => '⚡',
                'category' => 'Security'
            ],
            'cors' => [
                'title' => 'CORS Configuration',
                'file' => 'CORS.md',
                'icon' => '🌍',
                'category' => 'Security'
            ],
            'api-versioning' => [
                'title' => 'API Versioning',
                'file' => 'API-VERSIONING.md',
                'icon' => '📦',
                'category' => 'Advanced Features'
            ],
            'activity-log' => [
                'title' => 'Activity Logging',
                'file' => 'ACTIVITY-LOG.md',
                'icon' => '📝',
                'category' => 'Advanced Features'
            ],
            'file-upload' => [
                'title' => 'File Upload',
                'file' => 'FILE-UPLOAD.md',
                'icon' => '📎',
                'category' => 'Advanced Features'
            ],
            'email-verification' => [
                'title' => 'Email Verification',
                'file' => 'EMAIL-VERIFICATION.md',
                'icon' => '✉️',
                'category' => 'Advanced Features'
            ],
            'password-reset' => [
                'title' => 'Password Reset',
                'file' => 'PASSWORD-RESET.md',
                'icon' => '🔑',
                'category' => 'Advanced Features'
            ],
            'refresh-token' => [
                'title' => 'Refresh Token',
                'file' => 'REFRESH-TOKEN.md',
                'icon' => '🔄',
                'category' => 'Advanced Features'
            ],
            'troubleshooting' => [
                'title' => 'Troubleshooting',
                'file' => 'TROUBLESHOOTING.md',
                'icon' => '🔧',
                'category' => 'Help'
            ],
        ];
    }

    /**
     * Show documentation home page
     */
    public function index()
    {
        return view('documentation.index', [
            'grouped' => $this->getGroupedDocs(),
            'docs' => $this->docs
        ]);
    }

    /**
     * Show specific documentation
     */
    public function show($slug)
    {
        // Debug: Check if slug exists
        if (!isset($this->docs[$slug])) {
            // Log available slugs for debugging
            Log::info('Available slugs: ' . implode(', ', array_keys($this->docs)));
            Log::info('Requested slug: ' . $slug);
            abort(404, 'Documentation not found. Available: ' . implode(', ', array_keys($this->docs)));
        }

        $doc = $this->docs[$slug];
        $filePath = $this->docsPath . '/' . $doc['file'];

        if (!File::exists($filePath)) {
            Log::error('File not found: ' . $filePath);
            abort(404, 'Documentation file not found: ' . $doc['file']);
        }

        $markdown = File::get($filePath);
        $html = $this->parseMarkdown($markdown);

        return view('documentation.show', [
            'doc' => $doc,
            'content' => $html,
            'docs' => $this->docs,
            'grouped' => $this->getGroupedDocs(),
            'slug' => $slug
        ]);
    }

    /**
     * Get grouped documentation preserving keys
     */
    private function getGroupedDocs()
    {
        $grouped = [];
        foreach ($this->docs as $slug => $doc) {
            $category = $doc['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][$slug] = $doc;
        }
        return $grouped;
    }

    /**
     * Simple markdown parser
     */
    /**
     * Simple markdown parser with code block protection
     */
    private function parseMarkdown($markdown)
    {
        // 1. Extract Code Blocks to prevent markdown parsing inside them
        $codeBlocks = [];
        $markdown = preg_replace_callback(
            '/```([^\s]*)(?:\r?\n|\r)([\s\S]*?)(?:\r?\n|\r)```/m',
            function ($matches) use (&$codeBlocks) {
                $id = '%%CODE_BLOCK_' . count($codeBlocks) . '%%';
                $lang = trim($matches[1]);
                $code = $matches[2];
                // Default to plaintext if no lang specified
                if (empty($lang)) {
                    $lang = 'plaintext';
                }
                // Escape HTML entities
                $code = htmlspecialchars($code, ENT_NOQUOTES);

                $codeBlocks[$id] = '<pre><code class="language-' . $lang . '">' . $code . '</code></pre>';

                return "\n" . $id . "\n";
            },
            $markdown
        );

        // 2. Parse Markdown (Headers, Lists, etc.)

        // Headers
        // H1 - Centered and big spacing
        $markdown = preg_replace('/^# (.*?)$/m', '<h1 class="text-4xl font-bold mt-12 mb-8 text-gray-900 text-center border-b pb-4">$1</h1>', $markdown);
        // H2 - Big spacing
        $markdown = preg_replace('/^## (.*?)$/m', '<h2 class="text-3xl font-bold mt-12 mb-6 text-gray-800">$1</h2>', $markdown);
        // H3 - Moderate spacing
        $markdown = preg_replace('/^### (.*?)$/m', '<h3 class="text-2xl font-bold mt-10 mb-4 text-gray-800">$1</h3>', $markdown);
        // H4 - Small spacing
        $markdown = preg_replace('/^#### (.*?)$/m', '<h4 class="text-xl font-bold mt-8 mb-3 text-gray-800">$1</h4>', $markdown);

        // Bold
        $markdown = preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-semibold text-gray-900">$1</strong>', $markdown);

        // Italic
        $markdown = preg_replace('/\*(.*?)\*/', '<em class="italic">$1</em>', $markdown);

        // Inline code
        $markdown = preg_replace('/`([^`]+)`/', '<code class="bg-gray-800 text-green-400 px-1.5 py-0.5 rounded text-sm font-mono">$1</code>', $markdown);

        // Links
        $markdown = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" class="text-blue-600 hover:text-blue-800 underline transition-colors">$1</a>', $markdown);

        // Unordered Lists
        $markdown = preg_replace('/^\* (.+)$/m', '<li class="ml-6 mb-2 relative pl-2"><span class="absolute left-0 top-2 w-1.5 h-1.5 bg-gray-400 rounded-full"></span>$1</li>', $markdown);
        $markdown = preg_replace('/^- (.+)$/m', '<li class="ml-6 mb-2 relative pl-2"><span class="absolute left-0 top-2 w-1.5 h-1.5 bg-gray-400 rounded-full"></span>$1</li>', $markdown);
        $markdown = preg_replace('/(<li class="ml-6 mb-2 relative pl-2">.*?<\/li>\n?)+/', '<ul class="mb-6 space-y-1 text-gray-700">$0</ul>', $markdown);

        // Ordered Lists  
        $markdown = preg_replace('/^\d+\. (.+)$/m', '<li class="ml-6 mb-2 list-decimal">$1</li>', $markdown);
        $markdown = preg_replace('/(<li class="ml-6 mb-2 list-decimal">.*?<\/li>\n?)+/', '<ol class="mb-6 space-y-1 list-inside text-gray-700">$0</ol>', $markdown);

        // Tables
        $markdown = preg_replace_callback(
            '/\|(.+)\|\n\|[-:\s|]+\|\n((?:\|.+\|\n?)+)/m',
            function ($matches) {
                $header = $matches[1];
                $rows = $matches[2];

                $headerCells = array_map('trim', explode('|', trim($header, '|')));
                $headerHtml = '<thead><tr class="bg-gray-100">';
                foreach ($headerCells as $cell) {
                    $headerHtml .= '<th class="px-6 py-3 text-left text-sm font-semibold text-gray-900 border-b">' . trim($cell) . '</th>';
                }
                $headerHtml .= '</tr></thead>';

                $rowLines = array_filter(explode("\n", trim($rows)));
                $bodyHtml = '<tbody>';
                foreach ($rowLines as $row) {
                    $cells = array_map('trim', explode('|', trim($row, '|')));
                    $bodyHtml .= '<tr class="hover:bg-gray-50 transition-colors">';
                    foreach ($cells as $cell) {
                        $bodyHtml .= '<td class="px-6 py-4 text-sm text-gray-700 border-b">' . trim($cell) . '</td>';
                    }
                    $bodyHtml .= '</tr>';
                }
                $bodyHtml .= '</tbody>';

                return '<div class="overflow-x-auto mb-8 rounded-lg border border-gray-200 shadow-sm"><table class="min-w-full bg-white">' . $headerHtml . $bodyHtml . '</table></div>';
            },
            $markdown
        );

        // Blockquotes
        $markdown = preg_replace('/^> (.+)$/m', '<blockquote class="border-l-4 border-blue-500 pl-4 italic text-gray-700 my-6 bg-blue-50 py-3 rounded-r shadow-sm">$1</blockquote>', $markdown);

        // Horizontal rules
        $markdown = preg_replace('/^---$/m', '<hr class="my-10 border-gray-200">', $markdown);

        // Paragraphs
        $lines = explode("\n", $markdown);
        $result = [];
        foreach ($lines as $line) {
            // Check if line is a placeholder or html tag
            if (strpos($line, '%%CODE_BLOCK_') !== false || preg_match('/^<[h|u|o|p|b|d|t|l|s|e|a]/', trim($line))) {
                $result[] = $line;
            } elseif (trim($line) !== '') {
                // Add margins to paragraphs
                $result[] = '<p class="mb-6 text-gray-700 leading-relaxed text-lg">' . $line . '</p>'; // Increased margin and font size slightly
            }
        }
        $markdown = implode("\n", $result);

        // 3. Restore Code Blocks
        foreach ($codeBlocks as $id => $html) {
            $markdown = str_replace($id, $html, $markdown);
        }

        // Cleanup empty paragraphs
        $markdown = preg_replace('/<p class="[^"]+"><\/p>/', '', $markdown);

        return $markdown;
    }
}
