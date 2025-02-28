document.addEventListener("DOMContentLoaded", function() {
    const button = document.getElementById("fetch-emails-btn");
    const resultsContainer = document.getElementById("gmail-ai-results");

    if (button) {
        button.addEventListener("click", function() {
            button.innerText = "Fetching Emails...";
            button.disabled = true;

            // Ensure `ajaxurl` is correctly defined
            let ajaxUrl = ajax_object.ajax_url || document.querySelector("script[src*='admin-ajax.php']")?.src.split("?")[0];
            fetch(ajaxUrl + "?action=fetch_gmail_ai")
                .then(response => {
                    button.innerText = "Fetch Gmail Insights";
                    button.disabled = false;

                    if (!response.ok) {
                        resultsContainer.innerHTML = `<p style="color:red;">Error: ${response.statusText}</p>`;
                        return;
                    }

                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json().then(data => {
                            if (data.auth_url) {
                                resultsContainer.innerHTML = `<p>Please authorize the application by clicking <a href="${data.auth_url}" target="_blank">here</a>.</p>`;
                                return;
                            }

                            if (data.error) {
                                if (data.error === "Unauthorized access") {
                                    resultsContainer.innerHTML = `<p style="color:red;">Unauthorized access. Please <a href="${data.auth_url}" target="_blank">authorize the application</a>.</p>`;
                                } else {
                                    resultsContainer.innerHTML = `<p style="color:red;">Error: ${data.error}</p>`;
                                }
                                return;
                            }

                            let output = "<h4>AI Insights:</h4><ul>";
                            data.forEach(email => {
                                output += `
                                    <li>
                                        <strong>Subject:</strong> ${email.subject} <br>
                                        <strong>Snippet:</strong> ${email.snippet} <br>
                                        <strong>Sentiment:</strong> ${email.sentiment} <br>
                                        <strong>Summary:</strong> ${email.summary}
                                    </li><hr>`;
                            });
                            output += "</ul>";

                            resultsContainer.innerHTML = output;
                        });
                    } else {
                        return response.text().then(text => {
                            resultsContainer.innerHTML = `<p style="color:red;">Unexpected response format</p>`;
                            console.error("Unexpected response format:", text);
                        });
                    }
                })
                .catch(error => {
                    button.innerText = "Fetch Gmail Insights";
                    button.disabled = false;
                    resultsContainer.innerHTML = `<p style="color:red;">Failed to fetch data</p>`;
                    console.error("Error fetching emails:", error);
                });
        });
    }
});
