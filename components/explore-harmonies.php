<?php
/**
 * Explore Other Color Harmonies Component
 * World-class companion showcase for the 6 core color harmony systems.
 *
 * @var string $base
 * @var string|null $currentHarmony e.g. 'complementary', 'analogous', 'triadic', 'monochromatic', 'split-complementary', 'tetradic'
 */

$allHarmonies = [
    'complementary' => [
        'name'        => 'Complementary',
        'slug'        => 'complementary-colors',
        'badge'       => '180° Opposite',
        'icon'        => 'bi-circle-half',
        'tags'        => ['High Contrast', 'CTA Buttons'],
        'swatches'    => ['#0077FE', '#FE8800'],
        'swatch_names'=> ['Cobalt Blue', 'Vibrant Orange'],
        'description' => 'Two hues positioned directly opposite on the 360° color wheel. Generates maximum visual tension, perfect for buttons, call-to-actions, and high-impact accents.'
    ],
    'analogous' => [
        'name'        => 'Analogous',
        'slug'        => 'analogous-colors',
        'badge'       => '30° Adjacent',
        'icon'        => 'bi-water',
        'tags'        => ['Nature Inspired', 'Low Tension'],
        'swatches'    => ['#10B981', '#06B6D4', '#3B82F6'],
        'swatch_names'=> ['Emerald', 'Cyan', 'Blue'],
        'description' => 'Adjacent neighbors sitting side-by-side on the wheel. Delivers calm, serene transitions with organic cohesion inspired by ocean gradients and sunsets.'
    ],
    'triadic' => [
        'name'        => 'Triadic',
        'slug'        => 'triadic-colors',
        'badge'       => '120° Triangle',
        'icon'        => 'bi-triangle',
        'tags'        => ['Dynamic Energy', '120° Balance'],
        'swatches'    => ['#2563EB', '#EB2563', '#63EB25'],
        'swatch_names'=> ['Royal Blue', 'Hot Crimson', 'Vivid Lime'],
        'description' => 'Three equidistant hues spaced 120° apart forming an equilateral triangle. Offers vibrant energy and lively contrast while preserving optical balance.'
    ],
    'monochromatic' => [
        'name'        => 'Monochromatic',
        'slug'        => 'monochromatic-colors',
        'badge'       => 'Single Hue',
        'icon'        => 'bi-layers',
        'tags'        => ['Minimalist', 'Accessible'],
        'swatches'    => ['#1E1B4B', '#4338CA', '#6366F1', '#A5B4FC'],
        'swatch_names'=> ['Deep Navy', 'Indigo', 'Violet', 'Soft Lavender'],
        'description' => 'Pure variations in saturation and lightness derived from a single hue anchor. Creates elegant, minimal interfaces with foolproof contrast hierarchy.'
    ],
    'split-complementary' => [
        'name'        => 'Split-Complementary',
        'slug'        => 'split-complementary-colors',
        'badge'       => '150° + 210°',
        'icon'        => 'bi-diagram-3',
        'tags'        => ['Soft Tension', 'Versatile UI'],
        'swatches'    => ['#8B5CF6', '#F59E0B', '#10B981'],
        'swatch_names'=> ['Purple', 'Amber', 'Emerald'],
        'description' => 'Base hue paired with the two colors flanking its direct complement. Delivers rich nuance and high contrast with significantly softer tension than direct opposites.'
    ],
    'tetradic' => [
        'name'        => 'Tetradic',
        'slug'        => 'tetradic-colors',
        'badge'       => 'Double Pair',
        'icon'        => 'bi-bounding-box-circles',
        'tags'        => ['Rich Palette', 'Complex UI'],
        'swatches'    => ['#EC4899', '#8B5CF6', '#10B981', '#F59E0B'],
        'swatch_names'=> ['Pink', 'Purple', 'Teal', 'Amber'],
        'description' => 'Four hues arranged into two complementary pairs forming a rectangle on the wheel. Offers multifaceted variety and clear categorical distinction for dashboards.'
    ],
];

// Filter out the current harmony page so the other 5 companion systems are presented
$companionHarmonies = [];
foreach ($allHarmonies as $key => $harmony) {
    if (isset($currentHarmony) && $key === $currentHarmony) {
        continue;
    }
    $companionHarmonies[$key] = $harmony;
}
?>

<!-- ── Explore Other Color Harmonies ────────────────────────────────────────── -->
<section class="mt-20 rounded-3xl p-6 sm:p-8 md:p-10 bg-slate-50/70 dark:bg-slate-900/60 border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden" aria-labelledby="companion-harmonies-heading">
    <!-- Ambient Blur Background Accents -->
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-secondary/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative z-10">
        <!-- Section Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 pb-6 border-b border-slate-200/70 dark:border-slate-800">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold mb-3">
                    <i class="bi bi-compass"></i>
                    <span>Color Harmony System</span>
                </div>
                <h3 id="companion-harmonies-heading" class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                    Explore Other Color Harmonies
                </h3>
                <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2 max-w-2xl leading-relaxed">
                    Deepen your color theory expertise with our companion guides and interactive tools:
                </p>
            </div>
            <a href="<?= $base ?>/generate-palette" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/80 text-xs font-bold text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 shadow-sm transition-all hover:scale-105 shrink-0">
                <i class="bi bi-stars text-secondary"></i>
                <span>Try All in Palette Generator</span>
                <i class="bi bi-arrow-right text-primary"></i>
            </a>
        </div>

        <!-- 5-Card Balanced Studio Grid (3 on top row, 2 on bottom row) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
            <?php 
            $i = 0;
            foreach ($companionHarmonies as $key => $h): 
                // Row 1: 3 cards (each spans 2 of 6 columns)
                // Row 2: 2 cards (each spans 3 of 6 columns)
                $colSpanClass = ($i < 3) ? 'lg:col-span-2' : 'lg:col-span-3';
                $i++;
            ?>
                <a href="<?= $base ?>/<?= $h['slug'] ?>"
                   class="<?= $colSpanClass ?> group flex flex-col justify-between rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 hover:border-primary/50 shadow-sm hover:shadow-2xl hover:shadow-primary/10 transition-all duration-300 hover:-translate-y-1.5 overflow-hidden">
                    
                    <!-- Top Visual Harmony Palette Strip (Prominent Hero Element) -->
                    <div class="relative h-24 w-full flex overflow-hidden border-b border-slate-100 dark:border-slate-800">
                        <?php foreach ($h['swatches'] as $sIndex => $hex): ?>
                            <div class="flex-1 h-full relative group/swatch transition-transform duration-300 group-hover:scale-[1.03] flex items-end justify-center pb-2.5"
                                 style="background-color: <?= $hex ?>;"
                                 title="<?= $hex ?><?= isset($h['swatch_names'][$sIndex]) ? ' (' . $h['swatch_names'][$sIndex] . ')' : '' ?>">
                                <span class="font-mono text-[10px] font-bold text-white bg-black/50 backdrop-blur-sm px-1.5 py-0.5 rounded opacity-0 group-hover/swatch:opacity-100 transition-opacity pointer-events-none">
                                    <?= $hex ?>
                                </span>
                            </div>
                        <?php endforeach; ?>

                        <!-- Floating Frosted Glass Overlay Badges -->
                        <div class="absolute top-2.5 left-2.5 right-2.5 flex items-center justify-between pointer-events-none">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-black/45 backdrop-blur-md text-white text-[11px] font-bold shadow-sm">
                                <i class="bi <?= $h['icon'] ?>"></i>
                                <span><?= $h['name'] ?></span>
                            </span>
                            <span class="px-2.5 py-1 rounded-full bg-black/45 backdrop-blur-md text-white text-[11px] font-mono font-bold shadow-sm">
                                <?= $h['badge'] ?>
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5">
                                <h4 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                                    <?= $h['name'] ?>
                                </h4>
                                <div class="flex items-center gap-1.5">
                                    <?php foreach ($h['tags'] as $tag): ?>
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/50 dark:border-slate-700/50">
                                            <?= $tag ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                <?= $h['description'] ?>
                            </p>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="mt-5 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-xs font-bold text-primary group-hover:text-secondary transition-colors">
                            <span>Explore Guide &amp; Tools</span>
                            <i class="bi bi-arrow-right transform group-hover:translate-x-1.5 transition-transform text-sm"></i>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
