import os
import json
import sys
import requests

def main():
    print("Starting AI Code Reviewer...")

    # Load environment variables
    github_token = os.getenv("GITHUB_TOKEN")
    ai_api_key = os.getenv("AI_API_KEY")
    event_path = os.getenv("GITHUB_EVENT_PATH")

    if not github_token:
        print("Error: GITHUB_TOKEN environment variable is missing.")
        sys.exit(1)
    if not ai_api_key:
        print("Error: AI_API_KEY environment variable is missing.")
        sys.exit(1)
    if not event_path:
        print("Error: GITHUB_EVENT_PATH environment variable is missing. This script is intended to run inside GitHub Actions.")
        sys.exit(1)

    # Parse GitHub Webhook Event Payload
    try:
        with open(event_path, "r", encoding="utf-8") as f:
            event_data = json.load(f)
    except Exception as e:
        print(f"Error reading GitHub event payload: {e}")
        sys.exit(1)

    # Extract repository and PR info
    try:
        repo_name = event_data["repository"]["full_name"]
        pull_number = event_data["pull_request"]["number"]
    except KeyError as e:
        print(f"Error parsing PR metadata from event payload: {e}")
        sys.exit(1)

    print(f"Analyzing Pull Request #{pull_number} for repository {repo_name}...")

    # 1. Fetch Pull Request Diff
    diff_url = f"https://api.github.com/repos/{repo_name}/pulls/{pull_number}"
    headers = {
        "Authorization": f"Bearer {github_token}",
        "Accept": "application/vnd.github.v3.diff"
    }

    try:
        response = requests.get(diff_url, headers=headers)
        response.raise_for_status()
        pr_diff = response.text
    except Exception as e:
        print(f"Error fetching PR diff from GitHub: {e}")
        sys.exit(1)

    if not pr_diff.strip():
        print("No changes found in the PR diff.")
        sys.exit(0)

    print(f"Successfully fetched PR diff ({len(pr_diff)} characters). Calling Gemini API...")

    # 2. Call Gemini API for code review
    gemini_url = f"https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={ai_api_key}"
    
    prompt = (
        "You are an expert AI Code Reviewer. Review the following Git diff from a Pull Request.\n"
        "Please write your entire review feedback and explanations in Thai language (ภาษาไทย).\n"
        "Provide constructive, clear, and actionable feedback. Focus on:\n"
        "1. **Bugs & Logic Issues (บั๊กและปัญหาทางตรรกะ)**: Identify potential bugs, logical errors, edge cases, and crash risks.\n"
        "2. **Security (ความปลอดภัย)**: Identify vulnerabilities, hardcoded secrets, or insecure practices.\n"
        "3. **Readability & Style (ความอ่านง่ายและรูปแบบโค้ด)**: Suggest improvements for code quality, naming conventions, and structure.\n"
        "4. **Performance (ประสิทธิภาพ)**: Suggest optimization opportunities.\n\n"
        "Format your response in Markdown with clear sections and headings. If everything looks good, briefly mention that.\n\n"
        f"Here is the code diff:\n\n```diff\n{pr_diff}\n```"
    )


    payload = {
        "contents": [{
            "parts": [{
                "text": prompt
            }]
        }]
    }

    try:
        gemini_response = requests.post(gemini_url, json=payload)
        gemini_response.raise_for_status()
        result = gemini_response.json()
        
        # Extract response text
        review_text = result["candidates"][0]["content"]["parts"][0]["text"]
    except Exception as e:
        print(f"Error calling Gemini API: {e}")
        if 'gemini_response' in locals() and gemini_response.text:
            print(f"Response: {gemini_response.text}")
        sys.exit(1)

    print("Successfully received review from Gemini. Posting to GitHub PR...")

    # 3. Post review comment to GitHub PR
    comment_url = f"https://api.github.com/repos/{repo_name}/issues/{pull_number}/comments"
    comment_headers = {
        "Authorization": f"Bearer {github_token}",
        "Accept": "application/vnd.github.v3+json",
        "Content-Type": "application/json"
    }
    
    # Prepend a header to the comment
    formatted_comment = (
        "### 🤖 AI Code Reviewer Feedback\n\n"
        f"{review_text}\n\n"
        "*Reviewed by Gemini 2.5 Flash*"
    )

    comment_payload = {
        "body": formatted_comment
    }

    try:
        comment_response = requests.post(comment_url, headers=comment_headers, json=comment_payload)
        comment_response.raise_for_status()
    except Exception as e:
        print(f"Error posting review comment to GitHub: {e}")
        if 'comment_response' in locals() and comment_response.text:
            print(f"Response: {comment_response.text}")
        sys.exit(1)

    print("AI Review successfully posted to Pull Request!")

if __name__ == "__main__":
    main()
