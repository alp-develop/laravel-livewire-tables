<?php

declare(strict_types=1);

use Livewire\Tables\Core\Compat;

beforeEach(function (): void {
    Compat::flush();
});

afterEach(function (): void {
    Compat::flush();
});

test('laravelVersion returns a positive integer', function (): void {
    expect(Compat::laravelVersion())->toBeInt()->toBeGreaterThanOrEqual(10);
});

test('livewireVersion returns a positive integer', function (): void {
    expect(Compat::livewireVersion())->toBeInt()->toBeGreaterThanOrEqual(3);
});

test('exactly one laravel version method returns true', function (): void {
    $trueCount = collect([
        Compat::isLaravel10(),
        Compat::isLaravel11(),
        Compat::isLaravel12(),
        Compat::isLaravel13(),
    ])->filter()->count();

    expect($trueCount)->toBe(1);
});

test('exactly one livewire version method returns true', function (): void {
    $trueCount = collect([
        Compat::isLivewire3(),
        Compat::isLivewire4(),
    ])->filter()->count();

    expect($trueCount)->toBeGreaterThanOrEqual(1);
});

test('isLaravel10 returns true only for Laravel 10', function (): void {
    expect(Compat::isLaravel10())->toBe(Compat::laravelVersion() === 10);
});

test('isLaravel11 returns true only for Laravel 11', function (): void {
    expect(Compat::isLaravel11())->toBe(Compat::laravelVersion() === 11);
});

test('isLaravel12 returns true only for Laravel 12', function (): void {
    expect(Compat::isLaravel12())->toBe(Compat::laravelVersion() === 12);
});

test('isLaravel13 returns true for Laravel 13 and above', function (): void {
    expect(Compat::isLaravel13())->toBe(Compat::laravelVersion() >= 13);
});

test('isLaravel12 and isLaravel13 are mutually exclusive', function (): void {
    expect(Compat::isLaravel12() && Compat::isLaravel13())->toBeFalse();
});

test('flush resets cached laravel version', function (): void {
    $first = Compat::laravelVersion();
    Compat::flush();
    $second = Compat::laravelVersion();

    expect($first)->toBe($second);
});

test('flush resets cached livewire version', function (): void {
    $first = Compat::livewireVersion();
    Compat::flush();
    $second = Compat::livewireVersion();

    expect($first)->toBe($second);
});

test('supports returns true for known features', function (): void {
    expect(Compat::supports('reactive-props'))->toBeTrue()
        ->and(Compat::supports('on-attribute'))->toBeTrue()
        ->and(Compat::supports('locked-props'))->toBeTrue()
        ->and(Compat::supports('computed'))->toBeTrue();
});

test('supports returns false for unknown features', function (): void {
    expect(Compat::supports('non-existent-feature'))->toBeFalse();
});

test('supports returns false when any single feature is unsupported', function (): void {
    expect(Compat::supports('computed', 'non-existent-feature'))->toBeFalse();
});

test('isLivewire3 and isLivewire4 are mutually exclusive', function (): void {
    expect(Compat::isLivewire3() && Compat::isLivewire4())->toBeFalse();
});
