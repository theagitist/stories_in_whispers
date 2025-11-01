# Stories in Whispers - Poetry Building Game

A mystical HTML5 video game where players control a paddle to catch falling whispers (colored text blocks) representing different parts of speech, with the objective of building poetry (5-7-5 syllable poems). Experience a contemplative journey through floating verses and whispered dreams.

**Version 2.2.2** - Added gameplay sound effects and optimized audio latency

**Version 2.2.1** - Removed gameplay sound effects, kept only fireworks celebration sound

**Version 2.2.0** - Added animated celebration when finding all 4 reference windows

**Version 2.1.1** - Added references screens accessible via R key, updated prelude text, and removed config.php from repository

## How to Play

- Use the **left** and **right** arrow keys or **A/D** keys to move your paddle
- Press **SPACE** to start the game
- Press **SPACE** or **P** to pause/resume the game
- **Catch falling whispers to build poetry!** Collect words to create 5-7-5 syllable poems
- Multiple whispers fall simultaneously at different speeds
- Each whisper displays a word representing a different part of speech with color coding:
  - 🔴 **Red** = Verbs
  - 🟣 **Purple** = Nouns  
  - 🟡 **Yellow** = Adjectives
  - 🟢 **Green** = Adverbs
  - 🟠 **Orange** = Articles
  - 🩷 **Pink** = Conjunctions
  - ⚪ **White** = Special Power Whispers (POW!)
  - 🔵 **Light Blue** = Bomb Whispers (BOOM!) - Remove your last word

## Special Features

### Special Power Whispers
- **White POW! whispers**: Activate special powers when caught
- **Light Blue BOOM! whispers**: Remove your last collected word when caught

### Special Powers
- **Wide Paddle**: Makes your paddle wider for easier catching
- **Narrow Paddle**: Makes your paddle narrower for more challenge
- **Inverted Controls**: Reverses left/right movement controls
- **Ultra Speed**: Increases paddle movement speed
- **Exploding Whispers**: All regular whispers become dangerous and remove your last word
- **Upside Down**: Flips the entire screen upside down for 5 seconds
- **Whisper Rain**: Spawns many more whispers at once for intense gameplay
- **Hidden Words**: All words appear as "xxxxx" in dark gray for 5 seconds
- **Respite**: Clears screen and spawns only one whisper at a time for 5 seconds

### Visual Effects
- **Blinking animations**: Special whispers (POW!) blink to draw attention
- **Exploding whispers power**: All regular whispers blink light blue and show "BOOM!" when the power is active
- **Screen effects**: Special powers create visual feedback with border animations
- **Animated background**: Twinkling stars create a dynamic backdrop
- **Paddle design**: Clean underscore-based paddle design
- **Fade-out system**: Elements gradually disappear after 5 seconds of inactivity
- **Inspirational quotes**: Kawika Guillermo quote appears 1 second after elements fade out

## Contemplative Features

### Fade-Out System
- **5-second timer**: Elements start fading after 5 seconds of paddle inactivity
- **Gradual fade**: All game elements (stars, whispers, poem text) slowly disappear
- **Paddle remains**: Only the paddle stays visible during fade-out
- **UI preserved**: Story panel and instructions remain visible for reference

### Inspirational Moments
- **Kawika Guillermo quote**: "Stand still, and you cannot see very far. But start moving, and the world manifests around you."
- **1-second delay**: Quote appears 1 second after elements are fully hidden
- **Contemplative pause**: Creates a moment of reflection and inspiration
- **Immediate response**: Moving the paddle instantly fades out the quote and restores elements

## Game Mechanics

### Poetry Building
- **5-7-5 Structure**: First line has loosely 5 syllables, second line has 7, third line has 5
- **Real-time display**: Current poem is shown in the center of the screen
- **Word removal**: Bomb whispers and exploding whispers remove your last collected word
- **Automatic line breaks**: Words automatically move to the next line when syllable limits are reached

### Movement System
- **Acceleration**: Hold movement keys longer to move faster
- **Smooth controls**: Responsive paddle movement with momentum
- **Collision detection**: Precise hit detection for catching whispers

### Scoring System
- **Base points**: Earn points for each word caught
- **Poetry completion**: 100 bonus points for completing a poem
- **Progressive difficulty**: Game speed increases over time
- **Score tracking**: Current score and poetry progress displayed

## Technical Details

- **Built with**: HTML5 Canvas and JavaScript
- **No external dependencies**: Pure vanilla JavaScript
- **Responsive design**: Works on different screen sizes
- **Monospace font**: Consistent text rendering for whispers
- **Local storage**: Game state persistence

## Game Rules

1. **Catch whispers**: Use your paddle to catch falling word whispers
2. **Build poetry**: Collect words to create 5-7-5 syllable poems
3. **Use special powers**: White POW! whispers give you temporary abilities
4. **Avoid bombs**: Light blue BOOM! whispers remove your last word
5. **Complete poems**: Finish poems to earn bonus points and continue playing
6. **Don't let words fall**: Missed words are lost forever
7. **Take breaks**: Stop moving for 5 seconds to see elements fade and inspirational quotes appear

## Intro Screen

The game greets players with a personalized welcome message:
- **Personalized greeting**: Each player receives a unique poetic name (verb + adjective + noun)
- **Locale arrival**: Welcome message includes arrival to a mystical locale
- **Player identity**: "I see you are a [poetic name]. Let go of your mun and join this ethereal conversation."
- **References**: Press **R** to view references from Kawika Guillermo's book "Of Floating Isles" that inspired the game

## References Feature

Press **R** at any time to view references from the book that inspired this game:
- **Prelude screen**: View reference about the floating isle concept (page 15)
- **Intro screen**: View references about the game name, poetry concept, and player identity (pages 14-16, 34)
- **During gameplay**: View references about poetry building, dialogue concept, and Poem Gallery (pages 23, 37, 45)
- **Poem Gallery**: View reference about conversation and dialogue concept that inspired the gallery feature (page 37)

All references are contextual and explain the inspiration behind different game elements.

## Poem Gallery

Discover connections between poems through shared words. Access the gallery by pressing **G** during gameplay or **E** after completing a poem.

### Gallery Features
- **Word Connections**: View poems that share common words, grouped by connection strength
- **Statistics**: See overall stats, most used words, and player statistics
- **Random Poems**: Discover random poems from the collection
- **Visual Highlighting**: Common words between connected poems are highlighted with color-coded word types
- **Carousel Navigation**: Browse through poem connections with smooth transitions

### Gallery Controls
- **W** or **↑ Arrow**: Scroll up
- **S** or **↓ Arrow**: Scroll down
- **A** or **← Arrow**: Navigate to previous poem connection
- **D** or **→ Arrow**: Navigate to next poem connection
- **R**: View references for the current context
- **B**: Return to the Floating Isle (game)

## Controls

### In-Game Controls
- **Arrow Keys** or **A/D**: Move paddle left/right
- **SPACE**: Start game / Pause/Resume
- **P**: Pause/Resume (alternative)
- **R**: View references for current context (available in prelude, intro screen, during gameplay, and in gallery)
- **G**: Go to Poem Gallery
- **E**: Go to Poem Gallery (during gameplay)
- **L**: Leave locale (when game is paused)
- **X**: Fully restart (clears session and returns to prelude) - works at any point

## Tips for Success

- **Plan your poetry**: Think about syllable counts before catching words
- **Use special powers wisely**: Some powers help, others add challenge
- **Build momentum**: Hold movement keys for faster paddle speed
- **Practice**: The more you play, the better you'll get at poetry building
- **Take contemplative breaks**: Stop moving for 5 seconds to experience the fade-out and inspirational quotes
- **Avoid bomb whispers**: Light blue BOOM! whispers will remove your last word
- **Embrace the journey**: The game rewards both active play and thoughtful pauses

## Version History

### Version 2.2.2
- Added subtle catch sound effect when catching whispers (A4 note, 440Hz)
- Added explosion sound effect when catching bomb whispers (descending low frequencies)
- Added subtle chime sound effect when catching POW whispers (ascending C-E-G chord)
- Added explosion sound effect for exploding whispers special power
- Optimized audio scheduling for minimal latency (zero lookahead when audio context is running)
- Sound effects now play immediately on collision detection

### Version 2.2.1
- Removed all gameplay sound effects (catch sounds, miss sounds, haiku completion sounds, explosion sounds, special power sounds)
- Kept only the fireworks celebration sound effect that plays when finding all 4 reference windows
- Game now runs silently except for the reference discovery celebration

### Version 2.2.0
- Added animated text effect to "(Found 4 out of 4)" counter when all references are discovered
- Animation shows colorful pulsing and scaling effect for 2 seconds

### Version 2.1.1
- Added R key references feature accessible from prelude, intro screen, during gameplay, and in gallery
- Updated prelude text: Changed "Floating Isle" to "tiny isle floating in the ether"
- Updated prelude: Changed description from "commentary for an university course" to "a way to reflect on it"
- Updated prelude: Changed "Most elements refer" to "Concept, mechanics and other ideas refer"
- Updated intro screen text: Changed "A floating isle" to "A tiny isle floating in the ether"
- Changed references key from C to R throughout the game
- Added R key references screen to Poem Gallery
- Enabled E key to open gallery during gameplay (in addition to G key)
- Fixed R key handling on intro screen to work reliably
- Added X key to fully restart from anywhere (clears session and returns to prelude)
- Removed config.php from repository (added to .gitignore)
- Created config_blank.php as a template for database configuration
- Fixed references windows not working after page reload
- Added automatic full restart on page load/reload (always starts from prelude)
- Updated prelude text to include "Try to find all four!" hint about reference screens
- Added information in prelude about X key to restart the game
- Fixed prelude not closing after X key restart (re-registered keydown handler)
- Added reference counter showing "(Found X out of 4)" on all reference screens
- Added fireworks animation and celebratory sound effect when finding all 4 reference windows
- Added animated text effect to "(Found 4 out of 4)" counter when all references are discovered (2 second animation)

### Version 2.0.2
- Added keyboard navigation to Poem Gallery
- **Gallery scrolling**: W/S keys or Arrow Up/Down to scroll the gallery page
- **Carousel navigation**: A/D keys or Left/Right arrows to navigate poem connections
- **Navigation instructions**: Added on-screen instructions for gallery navigation
- Auto-rotate pauses when manually navigating connections

### Version 2.0.1
- Enhanced intro screen with personalized greeting
- Added "Let go of your mun and join this ethereal conversation" message
- Improved line breaks and formatting in intro screen

### Version 2.0.0
- Added fade-out system after 5 seconds of inactivity
- Implemented Kawika Guillermo inspirational quote
- Added bomb whispers (BOOM!) that remove words
- Updated terminology from "islets" to "whispers" for better thematic consistency
- Enhanced contemplative gameplay experience
- Cleaned up codebase and removed unused files
- Added Poem Gallery with word connection discovery

### Version 1.0.0
- Initial release with basic poetry building mechanics
- Special power system
- Progressive difficulty scaling

Enjoy the game and improve your parts of speech recognition and poetry writing skills!