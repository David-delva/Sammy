from pathlib import Path
import re

def update_file(path: Path, replacements: list[tuple[str, str]]) -> int:
    text = path.read_text(encoding='utf-8')
    original = text
    for old, new in replacements:
        text = text.replace(old, new)
    if text != original:
        path.write_text(text, encoding='utf-8')
        return 1
    return 0

css_path = Path('resources/css/app.css')
replacements = [
    ('var(--inst-text)', 'var(--text)'),
    ('var(--inst-text-soft)', 'var(--text-soft)'),
    ('var(--inst-text-muted)', 'var(--text-muted)'),
    ('var(--inst-gold-600)', 'var(--warning)'),
    ('rgba(92, 107, 192, 0.16)', 'rgba(63, 81, 181, 0.16)'),
    ('rgba(92, 107, 192, 0.10)', 'rgba(63, 81, 181, 0.10)'),
    ('rgba(123, 148, 214, 0.08)', 'rgba(79, 100, 210, 0.08)'),
    ('rgba(184, 212, 248, 0.34)', 'rgba(79, 100, 210, 0.24)'),
    ('rgba(184, 212, 248, 0.24)', 'rgba(79, 100, 210, 0.24)'),
    ('rgba(30, 77, 123, 0.16)', 'rgba(16, 38, 78, 0.16)'),
    ('rgba(30, 77, 123, 0.92)', 'rgba(16, 38, 78, 0.92)'),
    ('rgba(30, 77, 123, 0.55)', 'rgba(16, 38, 78, 0.55)'),
    ('#8b9fd9', '#3f51b5'),
    ('#5c6bc0', '#3f51b5'),
    ('#a1680d', 'var(--warning)'),
    ('#2e7d32', 'var(--success)'),
    ('#e8f5e9', '#d8f6dd'),
    ('#b8d4f8', '#96a4ff'),
    ('#7b94d6', '#3f51b5'),
    ('#6b7fd1', '#31439c'),
]
updates = update_file(css_path, replacements)
print(f'Updated {updates} files')
