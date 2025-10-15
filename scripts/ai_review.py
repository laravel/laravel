import subprocess
import os
import sys
import json
from openai import OpenAI

client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

def get_diff(branch):
    subprocess.run(["git", "fetch", "--all"], check=True)
    diff = subprocess.check_output(["git", "diff", f"origin/main...{branch}"]).decode()
    return diff

def review_code(diff):
    prompt = f"""
    You are a senior software architect. Review this git diff and:
    1. Point out issues or improvements.
    2. Suggest fixes as a unified diff if possible.

    Diff:
    {diff}
    """

    response = client.chat.completions.create(
        model="gpt-4o-mini",
        messages=[{"role": "user", "content": prompt}],
    )

    return response.choices[0].message.content.strip()

if __name__ == "__main__":
    branch = sys.argv[1]
    diff = get_diff(branch)
    review = review_code(diff)

    os.makedirs("output", exist_ok=True)
    with open("output/review.json", "w") as f:
        json.dump({"branch": branch, "review": review}, f, indent=2)

    print("✅ AI review complete. Output saved to output/review.json")
