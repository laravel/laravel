# Roadmap

## v2.x - Maintenance Mode

No new features are being worked on for v2.x.

## v3.0 - Brainstorming, no timeline

### Focus
- Nailing the mobile experience
- Fluid iteractions across devices
- Maintaining ease of setup

### Not planned for v3.0
The goal of this script from it's beginnings till today is to to provide a better *image viewing experience*.

- **HTML or video content:**  If you need to show html or video content, I recommend googling for an alternative script as there are many options.
- **Social sharing buttons**

### Image support
- **`srcset` and `<picture>`**: Investigate
- **EXIF rotation data**: Investigate
- **File formats**: All formats supported by the browser utilized? Test vector formats, e.g. SVG. Test animated, e.g. GIF.

### Interactions
- **Swipe gesture**: Allow swiping horizontally to navigate between images in a set. Up and down to exist.
- **Wide images on mobile**: Explore cropping when height would be reduced substantially on mobile devices. Allow swiping, and maybe tilt, support to view full image. Hint at behavior by doing small horizontal slide in when opening? What does tapping do? How does horizontal swiping affect navigation in sets?
- If user attempts to go forward when at end of image set, animation (shake?) indicating the end or option to close Lightbox.
- Make sure right-click/long pressing works to access the image's context menu.

### Layout
- Allow vertical centering.
- Update sizing on window resize.
- Should the dev be able to choose the position of the caption, close button, and nav controls?
- Optimize layout for mobile.
- Optimize layout for screens of varying densities.
- Should the close button still live in the bottom right corner?

### Animations
- **Start/end animation**: Animate towards the trigger.
- **Easing:** Speed up and include bounce? Options to control?
- **Performant animations**: Rewrite to use transforms exclusively.

### Assets
- Use inline SVG for UI elements.

### Caching
- **Preloading**: Review if and which images should be preloaded. Options?

### Error Handling
- What happens when an image url is incorrect?
- What happens when an image takes too long to load?

### Native behavior and accessibility
- **Right-click support**: Bring up image context menu. Allow saving and copying image.
- Should opening lightbox update the url? and should this url be parsed on page load to show Lightbox automatically?
- Review alt attributes.
- Review ARIA roles.
- Review constrast ratios.
- Review keyboard input and tabbing.
- Review click/touch target size.
- Test with screen reader.

### API
- Do not initialize automatically and allow multiple instances.
- Add event handlers.
- Allow setting options on the fly.
- Allow the setting of options from HTML?
- Evaluate preloading and caching.
- Allow placement inside of a specified element? Orig feature requester was dealing with iframe.

### Dependencies
- Drop jQuery requirement.
