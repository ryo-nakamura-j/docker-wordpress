<?php
function retrivefullname($name)
    {

        switch ($name) {
            case 'XSS':
                return 'Cross-site Scripting';
            case 'SQL':
                return 'SQL injection';
            case 'RCE':
                return 'Remote Code Execution';
            case 'LFI':
                return 'Local File Inclusion';
            case 'RFI':
                return 'Remote File Inclusion';
            case 'RLE':
                return 'Rate limiting Exceeded';
            case 'RLECrawler':
                return 'Rate limit Exceeded for crawler';
            default:
                return $name;

        }

    }