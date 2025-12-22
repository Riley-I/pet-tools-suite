(function () {
  function createUI(root) {
    root.innerHTML = `
      <div class="pettools-card">
        <h3 class="pettools-title">Puppy Weight Growth Predictor</h3>

        <label class="pettools-label">
          Age (weeks)
          <input class="pettools-input" type="number" min="1" max="104" step="1" name="age_weeks" value="12" />
        </label>

        <label class="pettools-label">
          Current weight (lbs)
          <input class="pettools-input" type="number" min="0.1" max="500" step="0.1" name="weight_lbs" value="10" />
        </label>

        <label class="pettools-label">
          Size class
          <select class="pettools-input" name="size_class">
            <option value="toy">Toy</option>
            <option value="small">Small</option>
            <option value="medium" selected>Medium</option>
            <option value="large">Large</option>
            <option value="giant">Giant</option>
          </select>
        </label>

        <button class="pettools-button" type="button">Calculate</button>

        <div class="pettools-result" aria-live="polite"></div>
      </div>
    `;

    const btn = root.querySelector(".pettools-button");
    const result = root.querySelector(".pettools-result");

    btn.addEventListener("click", async () => {
      const age_weeks = Number(root.querySelector('[name="age_weeks"]').value);
      const weight_lbs = Number(root.querySelector('[name="weight_lbs"]').value);
      const size_class = String(root.querySelector('[name="size_class"]').value);

      result.textContent = "Calculating…";

      try {
        const base = (window.PetTools && window.PetTools.restBase) || "/wp-json/pet-tools/v1/";
        const res = await fetch(base + "puppy-weight", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ age_weeks, weight_lbs, size_class })
        });

        const json = await res.json();

        if (!res.ok) {
          result.textContent = json?.message || "Something went wrong.";
          return;
        }

        const data = json.data;
        result.innerHTML = `
          <div><strong>Estimated adult weight:</strong> ${data.adult_weight_lbs_est} lbs</div>
          <div>Range: ${data.adult_weight_lbs_min}–${data.adult_weight_lbs_max} lbs</div>
          <div class="pettools-muted">Cached: ${json.cached ? "Yes" : "No"}</div>
        `;

        // Optional analytics hook
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          event: "pet_tool_calculate",
          tool: "puppy_weight",
          age_weeks,
          size_class
        });

      } catch (e) {
        result.textContent = "Network error. Please try again.";
      }
    });
  }

  function init() {
    const nodes = document.querySelectorAll('[data-pettools="puppy-weight"]');
    nodes.forEach(createUI);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
