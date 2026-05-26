#!/usr/bin/env python3
import json, os, shutil, sys

def build():
    pages_path = "data/pages.json"
    if not os.path.exists(pages_path):
        print("ERROR: pages.json not found")
        sys.exit(1)
    with open(pages_path, "r") as f:
        pages = json.load(f)
    with open("theme/base.html", "r") as f:
        template = f.read()
    if os.path.exists("build"):
        shutil.rmtree("build")
    os.makedirs("build")
    for page in pages:
        slug = page["slug"]
        html = template.replace("{{ content }}", page.get("body_content", ""))
        html = html.replace("{{ title }}", page.get("title", ""))
        html = html.replace("{{ description }}", page.get("description", ""))
        html = html.replace("{{ slug }}", slug)
        out_dir = os.path.join("build", slug) if slug else "build"
        os.makedirs(out_dir, exist_ok=True)
        with open(os.path.join(out_dir, "index.html"), "w") as f:
            f.write(html)
    if os.path.exists("assets"):
        shutil.copytree("assets", "build/assets", dirs_exist_ok=True)
    for fname in ["sitemap.xml", "send.php", "robots.txt"]:
        if os.path.exists(fname):
            shutil.copy(fname, os.path.join("build", fname))
    print(f"Built {len(pages)} pages")

if __name__ == "__main__":
    build()
