# Material Design Icons

This package includes individual files for each icon, ready to be imported into a project.

Each icon is in its own file, so you can bundle several icons from different icon sets without bundling entire icon sets.

## Installation

If you are using NPM:

```bash
npm install @iconify/icons-mdi --save-dev
```

If you are using Yarn:

```bash
yarn add --dev @iconify/icons-mdi
```

## Usage with React

First you need to install [Iconify for React](https://github.com/iconify/iconify/packages/react).

If you are using NPM:

```bash
npm install --save-dev @iconify/react
```

If you are using Yarn:

```bash
yarn add --dev @iconify/react
```

### Example using icon 'account-check' with React

```js
import { Icon, InlineIcon } from '@iconify/react';
import accountCheck from '@iconify/icons-mdi/account-check';
```

```jsx
<Icon icon={accountCheck} />
<p>This is some text with icon adjusted for baseline: <InlineIcon icon={accountCheck} /></p>
```

### Example using icon 'bell-alert-outline' with React

This example is using string syntax that is available since Iconify for React 2.0

This example will not work with Iconify for React 1.x

```jsx
import React from 'react';
import { Icon, addIcon } from '@iconify/react';
import bellAlertOutline from '@iconify/icons-mdi/bell-alert-outline';

addIcon('bellAlertOutline', bellAlertOutline);

export function MyComponent() {
	return (
		<div>
			<Icon icon="bellAlertOutline" />
		</div>
	);
}
```

### Example using icon 'calendar-edit' with React

```jsx
import React from 'react';
import { InlineIcon } from '@iconify/react';
import calendarEdit from '@iconify/icons-mdi/calendar-edit';

export function MyComponent() {
	return (
		<p>
			<InlineIcon icon={calendarEdit} /> Sample text with an icon.
		</p>
	);
}
```

See https://github.com/iconify/iconify/packages/react for details.

## Usage with Vue

First you need to install [Iconify for Vue](https://github.com/iconify/iconify/packages/vue).

If you are using NPM:

```bash
npm install --save-dev @iconify/vue
```

If you are using Yarn:

```bash
yarn add --dev @iconify/vue
```

### Example using icon 'account-check' with Vue

This example is using object syntax with TypeScript.

```vue
<template>
	<p>
		<iconify-icon :icon="icons.accountCheck" />
	</p>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator';
import IconifyIcon from '@iconify/vue';
import accountCheck from '@iconify/icons-mdi/account-check';

export default Vue.extend({
	components: {
		IconifyIcon,
	},
	data() {
		return {
			icons: {
				accountCheck: accountCheck,
			},
		};
	},
});
</script>
```

### Example using icon 'bell-alert-outline' with Vue

This example is using string syntax.

```vue
<template>
	<p>
		Example of 'bell-alert-outline' icon:
		<iconify-icon icon="bellAlertOutline" :inline="true" />!
	</p>
</template>

<script>
import IconifyIcon from '@iconify/vue';
import bellAlertOutline from '@iconify/icons-mdi/bell-alert-outline';

IconifyIcon.addIcon('bellAlertOutline', bellAlertOutline);

export default {
	components: {
		IconifyIcon,
	},
};
</script>
```

### Example using icon 'calendar-edit' with Vue

This example is using object syntax.

```vue
<template>
	<iconify-icon :icon="icons.calendarEdit" />
</template>

<script>
import IconifyIcon from '@iconify/vue';
import calendarEdit from '@iconify/icons-mdi/calendar-edit';

export default {
	components: {
		IconifyIcon,
	},
	data() {
		return {
			icons: {
				calendarEdit,
			},
		};
	},
};
</script>
```

See https://github.com/iconify/iconify/packages/vue for details.

## Usage with Svelte

First you need to install [Iconify for Svelte](https://github.com/iconify/iconify/packages/svelte).

If you are using NPM:

```bash
npm install --save-dev @iconify/svelte
```

If you are using Yarn:

```bash
yarn add --dev @iconify/svelte
```

### Example using icon 'account-check' with Svelte

```svelte
<script>
    // npm install --save-dev @iconify/svelte @iconify/icons-mdi
    import IconifyIcon from '@iconify/svelte';
    import accountCheck from '@iconify/icons-mdi/account-check';
</script>

<IconifyIcon icon={accountCheck} />
```

### Example using icon 'bell-alert-outline' with Svelte

```svelte
<script>
    // npm install --save-dev @iconify/svelte @iconify/icons-mdi
    import IconifyIcon from '@iconify/svelte';
    import bellAlertOutline from '@iconify/icons-mdi/bell-alert-outline';
</script>

<IconifyIcon icon={bellAlertOutline} />
```

### Example using icon 'calendar-edit' with Svelte

```svelte
<script>
    // npm install --save-dev @iconify/svelte @iconify/icons-mdi
    import IconifyIcon from '@iconify/svelte';
    import calendarEdit from '@iconify/icons-mdi/calendar-edit';
</script>

<IconifyIcon icon={calendarEdit} />
```

See https://github.com/iconify/iconify/packages/svelte for details.

## About Material Design Icons

Icons author: Austin Andrews

Website: https://github.com/Templarian/MaterialDesign

License: Open Font License

License URL: https://raw.githubusercontent.com/Templarian/MaterialDesign/master/LICENSE
