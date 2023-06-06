# Reading Time

## Overview

This WordPress plugin adds a widely utilized "reading time" feature to enhance the functionality of your blog posts. It
allows you to estimate and the provide your blog readers with reading time estimation, based on your preferences through the plugin's settings.

In addition, this plugin provides the flexibility to incorporate a translated label within your post template using a
shortcode. This enables you to strategically place the label wherever desired, enhancing the user experience for readers
in different languages.

## Installation

Here goes text for any generic plugin installation instructions.

## Usage

### Using Shortcode

use the `[reading time]` shortcode in any post template. That will print HTML markup with label and the reading time
duration.

### Using PHP Functions

`get_reading_time()`

Get the reading time duration string.

`the_reading_time()`

Echo the reading time duration string.

## Settings

### Words per Minute

You may select the amount of words that will be considered as 1 minute reading time.

### Supported Post Types

Select only the post types that you want this plugin to support. If shortcode is present on a post that is not on your
list - it will not print anything.

### Rounding

Select between multiple choices of rounding the calculated reading time seconds.

## TODOs

Look out for my TODOs :)