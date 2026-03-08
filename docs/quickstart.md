# 🚀 Quick Start Guide - Phantom.ai Workflow Automation

Get started with Phantom.ai's WordPress development workflow automation in minutes.

## Installation

```bash
# Clone and install
git clone https://github.com/DemeWebsolutions/Phantom.ai.git
cd Phantom.ai
composer install

# Verify installation
./phantom-ai/phantom-cli help
```

## Your First Task

### Step 1: Classify a Task
```bash
./phantom-ai/phantom-cli classify "Create a testimonial slider block"
```

**Output:**
```
Task Classification
===================
Tier: high
Task Type: code_generation
Reason: Requires code implementation
Cost Multiplier: 20x
```

### Step 2: Process Through Workflow
```bash
./phantom-ai/phantom-cli process "Create a testimonial slider block in blocks/testimonials/block.json"
```

**Output:**
```
Processing task: task-abc123
Status: ready_for_execution
Tier: high
Task Type: code_generation
Copilot Ready: Yes

Use 'phantom-cli copilot task-abc123' to generate Copilot prompt.
```

### Step 3: Generate Copilot Prompt
```bash
./phantom-ai/phantom-cli copilot task-abc123
```

**Output:**
```
=== Copilot-Ready Prompt ===

ROLE:
You are a WordPress plugin developer working on a block-first hybrid theme.

PROJECT CONTEXT:
- Phantom.ai manages task routing and verification
- High-tier code execution is your responsibility
...
```

### Step 4: Use the Prompt
1. Copy the generated prompt
2. Open GitHub Copilot
3. Paste the prompt
4. Copilot generates the code
5. Review and implement

## Common Use Cases

### Use Case 1: Code Generation
```bash
# For creating new WordPress blocks, plugins, or features
./phantom-ai/phantom-cli process "Build a product carousel for WooCommerce in blocks/carousel/block.json"
```
→ Routes to HIGH tier (Copilot)

### Use Case 2: Code Review
```bash
# For security, compliance, or quality reviews
./phantom-ai/phantom-cli process "Review security vulnerabilities in payment processing"
```
→ Routes to MID tier (automated review)

### Use Case 3: Testing
```bash
# For running tests and validations
./phantom-ai/phantom-cli process "Run unit tests on authentication module"
```
→ Routes to MID tier (automated testing)

### Use Case 4: Questions & Planning
```bash
# For information or planning questions
./phantom-ai/phantom-cli process "What are WordPress block registration best practices?"
```
→ Routes to CHEAP tier (quick response)

## Understanding Task Classification

### Task Types

| Type | Tier | Cost | When to Use |
|------|------|------|-------------|
| Basic Response | Cheap | 1x | Questions, documentation, planning |
| Review | Mid | 5x | Code reviews, security audits, validation |
| Testing | Mid | 5x | Running tests, verification, QA |
| Code Generation | High | 20x | Creating blocks, plugins, features |

### Keywords That Trigger Classification

**Code Generation (High Tier):**
- create, build, implement, develop, generate
- "custom block", "plugin", "theme"

**Review (Mid Tier):**
- review, check, validate, verify, analyze, inspect

**Testing (Mid Tier):**
- test, testing, run tests, unit test

**Basic Response (Cheap Tier):**
- explain, what, how, when, why, document

## Viewing Performance

### Quick Stats
```bash
./phantom-ai/phantom-cli stats
```

### Full Report with Recommendations
```bash
./phantom-ai/phantom-cli report
```

**Sample Output:**
```
=== Phantom.ai Performance Report ===

Total Tasks: 15
Success Rate: 86.67%
Average Iterations: 1.2

Tier Distribution:
  - Cheap: 3 (20.0%)
  - Mid: 5 (33.3%)
  - High: 7 (46.7%)

=== Optimization Recommendations ===

[high] cost_optimization: High-tier usage is 46.7%. 
Consider better task classification to reduce costs.
```

## Using WordPress Templates

### Creating a Block

```bash
# Copy the block template
cp -r phantom-ai/Templates/WordPress/block-template blocks/my-block

# Edit the files
cd blocks/my-block
# - Modify block.json for your block metadata
# - Update index.js for functionality
# - Customize editor.css and style.css
```

### Creating a Plugin

```bash
# Copy the plugin template
cp phantom-ai/Templates/WordPress/plugin-template.php my-plugin.php

# Edit for your plugin
# - Update plugin header
# - Add your functionality
# - Register your blocks
```

## Working with SVG Assets

The system includes two SVG designs:
- `phantom-ai/Assets/phantom-ai-01.svg` - Primary logo
- `phantom-ai/Assets/phantom-ai-02.svg` - Neural network design

**Usage Rules:**
- ✅ Use as-is without modifications
- ✅ Maintain original viewBox and aspect ratio
- ❌ Don't rasterize to PNG/JPG
- ❌ Don't inject inline styles into paths

## Tips for Success

### 1. Be Specific with File Names
❌ Bad: "Create a product block"
✅ Good: "Create a product block in blocks/products/block.json"

### 2. Include Context
❌ Bad: "Add filtering"
✅ Good: "Add category filtering to the product grid block"

### 3. One Task at a Time
❌ Bad: "Create a block, add styling, and write tests"
✅ Good: "Create a product grid block" (then separate tasks for styling and tests)

### 4. Let the System Route Tasks
The system automatically determines the best tier based on your task description. Trust the classification!

## Troubleshooting

### Task Needs Clarification
If you see "clarification_needed", the system needs more information:
```
Questions:
  - Which files need to be created or modified?
```
Provide more specific details in your task description.

### Wrong Tier Classification
If a task is misclassified, you can:
1. Rephrase your task description
2. Include specific keywords from the classification table
3. Be more explicit about what needs to be done

### CLI Not Working
```bash
# Regenerate autoloader
composer dump-autoload

# Check PHP version
php -v  # Should be 7.4 or higher

# Verify file permissions
chmod +x ./phantom-ai/phantom-cli
```

## Next Steps

1. **Run the example workflow**
   ```bash
   ./examples/workflow-example.sh
   ```

2. **Read the full documentation**
   - [PHANTOM-WORKFLOW.md](PHANTOM-WORKFLOW.md) - Complete workflow guide
   - [WORKFLOW-DIAGRAM.md](WORKFLOW-DIAGRAM.md) - Visual workflow
   - [phantom-ai/README.md](phantom-ai/README.md) - Component reference

3. **Try with your own tasks**
   Start with simple tasks and work up to complex code generation

4. **Monitor performance**
   Use `stats` and `report` commands to track your efficiency

## Support

For issues or questions:
- Review the documentation in the `docs/` directory
- Check existing issues on GitHub
- Contact: https://demewebsolutions.com

## License

Proprietary software — all rights reserved.

For licensing inquiries: https://demewebsolutions.com/phantom-ai

---

**Ready to automate your WordPress development workflow?** 🚀

Start with: `./phantom-ai/phantom-cli help`
