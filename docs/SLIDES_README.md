# How to Present the Features Slides

The slide deck is in **`FEATURES_SLIDES.md`**. Each `---` separates one slide.

## Option 1: Marp (recommended)

[Marp](https://marp.app/) turns Markdown into slides.

1. **VS Code:** Install the "Marp for VS Code" extension, open `FEATURES_SLIDES.md`, then use "Open Preview to the Side" and choose the Marp preview.
2. **CLI:** Install Marp CLI (`npm i -g @marp-team/marp-cli`), then:
   ```bash
   cd docs
   marp FEATURES_SLIDES.md -o FEATURES_SLIDES.html
   # or PDF:
   marp FEATURES_SLIDES.md -o FEATURES_SLIDES.pdf
   ```
   Open the generated HTML or PDF in a browser or viewer.

## Option 2: Copy into PowerPoint / Google Slides

- Copy each slide (the text between `---` markers) into a title + bullet slide.
- Slide 1 = title; subsequent slides = section title + content.

## Option 3: Reveal.js or similar

- Use a Markdown-to-Reveal.js converter, or paste the content into a Reveal.js Markdown section.

## Slide count

There are **18 content slides** plus a title and thank-you slide (20 total).
