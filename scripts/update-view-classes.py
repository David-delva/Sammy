from pathlib import Path
import re

root = Path('resources/views')
files = list(root.rglob('*.blade.php')) + list(root.rglob('*.php'))
replacements = [
    (r'\btext-brand-(\d{2,3})\b', r'text-cobalt-\1'),
    (r'\bbg-brand-(\d{2,3})(?:/\d{1,3})?\b', r'bg-cobalt-\1'),
    (r'\bborder-brand-(\d{2,3})\b', r'border-cobalt-\1'),
    (r'\bhover:text-brand-(\d{2,3})\b', r'hover:text-cobalt-\1'),
    (r'\bhover:border-brand-(\d{2,3})\b', r'hover:border-cobalt-\1'),
    (r'\bhover:bg-brand-(\d{2,3})(?:/\d{1,3})?\b', r'hover:bg-cobalt-\1'),
    (r'\bfocus:ring-brand-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'focus:ring-cobalt-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bfrom-brand-(\d{2,3})\b', r'from-cobalt-\1'),
    (r'\bto-brand-(\d{2,3})\b', r'to-navy-\1'),
    (r'\bshadow-brand-(\d{2,3})/(\d{1,2})\b', r'shadow-cobalt-\1/\2'),
    (r'\btext-blue-(\d{2,3})\b', r'text-cobalt-\1'),
    (r'\bborder-blue-(\d{2,3})\b', r'border-cobalt-\1'),
    (r'\bbg-blue-(\d{2,3})(?:/\d{1,3})?\b', r'bg-cobalt-\1'),
    (r'\bhover:text-blue-(\d{2,3})\b', r'hover:text-cobalt-\1'),
    (r'\bhover:border-blue-(\d{2,3})\b', r'hover:border-cobalt-\1'),
    (r'\bhover:from-blue-(\d{2,3})\b', r'hover:from-cobalt-\1'),
    (r'\bhover:to-blue-(\d{2,3})\b', r'hover:to-navy-\1'),
    (r'\bhover:bg-blue-(\d{2,3})(?:/\d{1,3})?\b', r'hover:bg-cobalt-\1'),
    (r'\bfrom-blue-(\d{2,3})\b', r'from-cobalt-\1'),
    (r'\bto-blue-(\d{2,3})\b', r'to-navy-\1'),
    (r'\bfocus:ring-blue-(\d{2,3})\b', r'focus:ring-cobalt-\1'),
    (r'\bshadow-blue-(\d{2,3})/(\d{1,2})\b', r'shadow-cobalt-\1/\2'),
    (r'\btext-emerald-(\d{2,3})\b', r'text-success-\1'),
    (r'\bbg-emerald-(\d{2,3})(?:/\d{1,3})?\b', r'bg-success-\1'),
    (r'\bborder-emerald-(\d{2,3})\b', r'border-success-\1'),
    (r'\btext-green-(\d{2,3})\b', r'text-success-\1'),
    (r'\bbg-green-(\d{2,3})(?:/\d{1,3})?\b', r'bg-success-\1'),
    (r'\bborder-green-(\d{2,3})\b', r'border-success-\1'),
    (r'\btext-amber-(\d{2,3})\b', r'text-warning-\1'),
    (r'\bbg-amber-(\d{2,3})(?:/\d{1,3})?\b', r'bg-warning-\1'),
    (r'\bborder-amber-(\d{2,3})\b', r'border-warning-\1'),
    (r'\btext-red-(\d{2,3})\b', r'text-danger-\1'),
    (r'\bbg-red-(\d{2,3})(?:/\d{1,3})?\b', r'bg-danger-\1'),
    (r'\bborder-red-(\d{2,3})\b', r'border-danger-\1'),
    (r'\bring-brand-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'ring-cobalt-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bring-blue-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'ring-cobalt-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bring-emerald-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'ring-success-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bring-green-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'ring-success-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bring-amber-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'ring-warning-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bring-red-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'ring-danger-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bhover:ring-brand-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'hover:ring-cobalt-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bhover:ring-blue-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'hover:ring-cobalt-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bhover:ring-emerald-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'hover:ring-success-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bhover:ring-green-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'hover:ring-success-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bhover:ring-amber-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'hover:ring-warning-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bhover:ring-red-(\d{2,3})(?:/(\d{1,3}))?\b', lambda m: f'hover:ring-danger-{m.group(1)}' + (f'/{m.group(2)}' if m.group(2) else '')),
    (r'\bshadow-emerald-(\d{2,3})/(\d{1,2})\b', r'shadow-success-\1/\2'),
    (r'\bshadow-green-(\d{2,3})/(\d{1,2})\b', r'shadow-success-\1/\2'),
    (r'\bshadow-amber-(\d{2,3})/(\d{1,2})\b', r'shadow-warning-\1/\2'),
    (r'\bshadow-red-(\d{2,3})/(\d{1,2})\b', r'shadow-danger-\1/\2'),
]

for path in files:
    content = path.read_bytes().decode('utf-8', errors='replace')
    orig = content
    for pattern, repl in replacements:
        content = re.sub(pattern, repl, content)
    if content != orig:
        path.write_text(content, encoding='utf-8')
        print(f'Updated {path}')
