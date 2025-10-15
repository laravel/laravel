import os
import sys
import requests
import json

USE_DUMMY = "true" == "true"

JIRA_URL = os.getenv("JIRA_URL")
JIRA_USER = os.getenv("JIRA_USER")
JIRA_TOKEN = os.getenv("JIRA_TOKEN")

def post_to_jira(issue_key, pr_url):
    api_url = f"{JIRA_URL}/rest/api/3/issue/{issue_key}/comment"
    auth = (JIRA_USER, JIRA_TOKEN)
    headers = {"Content-Type": "application/json"}

    # --- Dummy mode content ---
    #if USE_DUMMY:
    print("🤖 Dummy mode ON — posting fake data to real Jira.")
    payload = {
        "body": {
            "type": "doc",
            "version": 1,
            "content": [
                {
                    "type": "paragraph",
                    "content": [
                        {"type": "text", "text": "✅ Test comment via Python script."}
                    ],
                },
                {
                    "type": "paragraph",
                    "content": [
                        {"type": "text", "text": "This is using the ADF structure and should appear correctly in Jira."}
                    ],
                },
                {
                    "type": "paragraph",
                    "content": [
                        {
                            "type": "text",
                            "text": "Check out dummy PR link",
                        },
                        {
                            "type": "text",
                            "text": " here.",
                            "marks": [
                                {"type": "link", "attrs": {"href": "https://dummy-pr-link.example.com"}}
                            ],
                        },
                    ],
                },
            ],
        }
    }
    #else:
        #payload = {"body": f"🤖 AI-generated fix PR created: {pr_url}"}


    res = requests.post(api_url, auth=auth, headers=headers, data=json.dumps(payload))

    if res.status_code not in [200, 201]:
        print(f"❌ Jira response {res.status_code}: {res.text}")
        return False

    print(f"✅ Posted to Jira issue {issue_key} ({'dummy' if USE_DUMMY else 'real'} data).")
    return True


if __name__ == "__main__":
    issue_key = sys.argv[1]

    # Fallback dummy PR link if none exists
    pr_url = "https://dummy-pr-link.example.com"
    if os.path.exists("output/pr_url.txt"):
        with open("output/pr_url.txt") as f:
            pr_url = f.read().strip()

    post_to_jira(issue_key, pr_url)
