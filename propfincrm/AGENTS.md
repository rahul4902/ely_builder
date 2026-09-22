# ElyLeads UI Conventions

Apply these conventions to every new or modified application screen unless the
user explicitly requests a different pattern.

- Use the shared `<x-form.input>` component for text, email, password, file,
  textarea, checkbox, and select controls. Extend that component when a new
  input capability is required instead of adding a raw `<input>`, `<select>`,
  or `<textarea>` in a view.
- Use the lead-list action pattern for DataTable row actions: an icon-only
  three-dot (`fa-ellipsis`) dropdown that contains the available actions.
  Do not use a labelled "Action" button or inline Edit/Delete buttons.
- Keep row-action menus compact, right-aligned, and consistent with the lead
  list's edit and destructive-action treatments.
