from pathlib import Path
import re

path = Path('resources/css/app.css')
text = path.read_text(encoding='utf-8')
patterns = [
    r'#8b9fd9',
    r'#5c6bc0',
    r'rgba\(123, 148, 214',
    r'rgba\(92, 107, 192',
    r'rgba\(184, 212, 248',
    r'rgba\(30, 77, 123',
    r'var\(--inst-',
    r'bg-brand-',
    r'bg-blue-',
    r'text-brand-',
    r'text-blue-',
    r'border-brand-',
    r'border-blue-',
    r'from-blue',
    r'from-brand',
    r'to-blue',
    r'to-brand',
    r'shadow-blue',
    r'shadow-brand',
]
for p in patterns:
    m = re.findall(p, text)
    if m:
        print(p, len(m))
