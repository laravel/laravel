import subprocess
import openai
import os
import sys
import json

openai.api_key = os.getenv("OPENAI_API_KEY")

def get_diff(branch):
    try:
        subprocess.run(["git", "fetch", "origin", "main", branch], check=True)
        diff = subprocess.check_output(["git", "diff", f"origin/main...{branch}"]).decode()
        return diff
    except subprocess.CalledProcessError as e:
        print("⚠️ Could not diff branches:", e)
        # fallback: diff last 10 commits
        return subprocess.check_output(["git", "diff", "HEAD~10..HEAD"]).decode()


def review_code(diff):
    prompt = f"""
    You are a senior software architect. Review this diff carefully:
    {diff}

    - Point out code issues or bad practices.
    - Suggest improvements or small fixes.
    - Return a unified diff-style patch if you can automatically fix issues.
    """
    response = openai.ChatCompletion.create(
        model="gpt-4o",
        messages=[{"role": "user", "content": prompt}]
    )
    return response.choices[0].message.content.strip()

if __name__ == "__main__":
    branch = sys.argv[1]
    diff = get_diff(branch)
    review = review_code(diff)
    os.makedirs("output", exist_ok=True)
    with open("output/review.json", "w") as f:
        json.dump({"branch": branch, "review": review}, f, indent=2)
    print("✅ Review done. Result saved in output/review.json")
