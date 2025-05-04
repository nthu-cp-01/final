# Copilot Guidelines for Development

## Architecture Guidelines
- This project uses Laravel 12 with Vue.js and Inertia.js
- Frontend stack: TailwindCSS, shadcn-vue, and TypeScript
- Database: PostgreSQL with Eloquent ORM

## Implementation Guidelines
- Follow MVC pattern and Laravel conventions
- Create migration, model, factory, and controller together using `php artisan make:model -mfc ModelName`
- Use Eloquent ORM for database operations, avoid raw SQL
- Install required shadcn-vue components before using them via: `npx shadcn-vue@latest add component-name`

## UI Structure Guidelines
- Layout structure matters! Follow these patterns:
  - Wrap page content in: `<div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">`
  - Use proper container borders: `border border-sidebar-border/70 bg-white dark:border-sidebar-border dark:bg-black`
  - Ensure padding inside containers: `p-6` for content areas
- For tables:
  - Always wrap tables in `<div class="overflow-x-auto">` to prevent horizontal overflow
  - Specify column widths using percentage values: `w-[20%]` 
  - Use `truncate` class for cells that might contain long text
  - Use flex layouts for action buttons: `<div class="flex justify-end gap-2">`
- Match dashboard's style & structure for consistent UI experience

## Component Structure
- Use the AppLayout component as the base for all pages
- Properly structure breadcrumbs following existing patterns
- Place <Head> component before <AppLayout> for proper page title

## Responsiveness
- Ensure designs work on multiple screen sizes (including 16:10 displays)
- Test layouts with both expanded and collapsed sidebar states
- Use responsive classes (sm:, md:, lg:) when needed to adapt layouts