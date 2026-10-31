<?php

namespace App\Helpers;

class RichTextHelper
{
    /**
     * Parse text containing Markdown code fences (```lang ... ```) and inline code (`...`)
     * securely into formatted HTML with syntax highlighting containers.
     */
    public static function format(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. First extract and protect code blocks
        $codeBlocks = [];
        $blockIndex = 0;

        // Replace ```lang ... ```
        $text = preg_replace_callback('/```([a-zA-Z0-9_\-\+#]*)\r?\n?([\s\S]*?)```/', function ($matches) use (&$codeBlocks, &$blockIndex) {
            $lang = strtolower(trim($matches[1])) ?: 'clike';
            $code = htmlspecialchars(trim($matches[2]), ENT_QUOTES, 'UTF-8');
            $placeholder = "___CODE_BLOCK_{$blockIndex}___";
            
            $codeBlocks[$placeholder] = '<div class="code-block-container my-3 rounded-xl overflow-hidden border border-[#1A2820] bg-[#090D0B] shadow-lg">'
                . '<div class="px-3.5 py-1.5 bg-[#0F1713] border-b border-[#1A2820] flex items-center justify-between text-[11px] font-mono text-gray-400">'
                . '<span class="uppercase tracking-wider font-bold text-emerald-400">' . htmlspecialchars($lang) . '</span>'
                . '<span class="text-[10px] text-gray-500">Kode Program</span>'
                . '</div>'
                . '<pre class="p-3.5 text-xs font-mono overflow-x-auto text-emerald-300 leading-relaxed m-0"><code class="language-' . htmlspecialchars($lang) . '">' . $code . '</code></pre>'
                . '</div>';
            
            $blockIndex++;
            return $placeholder;
        }, $text);

        // 2. Protect inline code `...`
        $inlineBlocks = [];
        $inlineIndex = 0;
        $text = preg_replace_callback('/`([^`\n\r]+)`/', function ($matches) use (&$inlineBlocks, &$inlineIndex) {
            $code = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
            $placeholder = "___INLINE_CODE_{$inlineIndex}___";
            $inlineBlocks[$placeholder] = '<code class="px-1.5 py-0.5 rounded-md bg-[#142019] border border-[#1E3327] text-emerald-300 font-mono text-xs font-bold">' . $code . '</code>';
            $inlineIndex++;
            return $placeholder;
        }, $text);

        // 3. Escape standard text
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // 4. Convert newlines to <br>
        $text = nl2br($text);

        // 5. Restore inline code
        foreach ($inlineBlocks as $placeholder => $html) {
            $text = str_replace($placeholder, $html, $text);
        }

        // 6. Restore code blocks
        foreach ($codeBlocks as $placeholder => $html) {
            $text = str_replace($placeholder, $html, $text);
        }

        return $text;
    }
}
