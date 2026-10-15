/**
 * SVG graphics the game draws, as paths under public/. Swap a file or a path to restyle something;
 * remove an entry to fall back to the built-in drawing. Sizes and orientation: public/assets/README.md.
 */
export default {
    units: {
        imp: 'assets/units/imp.svg',
        orc: 'assets/units/orc.svg',
        warlock: 'assets/units/warlock.svg',
        troll: 'assets/units/troll.svg',
        mistress: 'assets/units/mistress.svg',
        tunneller: 'assets/units/tunneller.svg',
        archer: 'assets/units/archer.svg',
        wizard: 'assets/units/wizard.svg',
        knight: 'assets/units/knight.svg',
        lord: 'assets/units/lord.svg',
    },
    tiles: {
        earth: 'assets/tiles/earth.svg',
        gold: 'assets/tiles/gold.svg',
        gem: 'assets/tiles/gem.svg',
        rock: 'assets/tiles/rock.svg',
        floor: 'assets/tiles/floor.svg',
        claimed: 'assets/tiles/claimed.svg',
        wall: 'assets/tiles/wall.svg',
    },
    props: {
        heart: 'assets/props/heart.svg',
        portal: 'assets/props/portal.svg',
        "portal-unclaimed": 'assets/props/portal-unclaimed.svg',
        gate: 'assets/props/gate.svg',
        chest: 'assets/props/chest.svg',
        "treasury-pile": 'assets/props/treasury-pile.svg',
        "lair-nest": 'assets/props/lair-nest.svg',
        "hatchery-chicken": 'assets/props/hatchery-chicken.svg',
        "training-dummy": 'assets/props/training-dummy.svg',
        "library-shelf": 'assets/props/library-shelf.svg',
    },
};
