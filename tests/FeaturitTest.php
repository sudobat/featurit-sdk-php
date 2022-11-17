<?php

namespace Featurit\Client\Tests;

use ArgumentCountError;
use Exception;
use Featurit\Client\Featurit;
use PHPUnit\Framework\TestCase;

class FeaturitTest extends TestCase
{
    const TENANT_IDENTIFIER = "agrosingularity";

    const INVALID_API_KEY = "2";
    const VALID_API_KEY = "e49e2918-24cc-4d04-8208-ecef34d51daa";

    const NON_EXISTING_FEATURE_NAME = "NON_EXISTING_FEATURE_NAME";
    const EXISTING_INACTIVE_FEATURE_NAME = "Login";
    const EXISTING_ACTIVE_FEATURE_NAME = "Sign Up";

    public function test_featurit_sdk_cannot_be_instantiated_without_api_key(): void
    {
        $this->expectException(ArgumentCountError::class);

        $featurit = new Featurit();
    }

    public function test_featurit_throws_exception_with_invalid_api_key(): void
    {
        $this->expectException(Exception::class);

        $featurit = new Featurit(self::TENANT_IDENTIFIER, self::INVALID_API_KEY);

        $featurit->featureFlags()->all();
    }

    /**
     * @throws \Http\Client\Exception
     */
    public function test_featurit_returns_array_with_features(): void
    {
        $featurit = new Featurit(self::TENANT_IDENTIFIER, self::VALID_API_KEY);

        $featureFlags = $featurit->featureFlags()->all();

        $this->assertIsArray($featureFlags);
    }

    public function test_featurit_feature_flags_have_string_keys(): void
    {
        $featurit = new Featurit(self::TENANT_IDENTIFIER, self::VALID_API_KEY);

        $featureFlags = $featurit->featureFlags()->all();

        $allOfTheValuesAreStrings = true;
        foreach($featureFlags as $featureFlagName => $isActive) {
            if (!is_string($featureFlagName)) {
                $allOfTheValuesAreStrings = false;
                break;
            }
        }

        $this->assertTrue($allOfTheValuesAreStrings);
    }

    public function test_featurit_feature_flags_have_boolean_values(): void
    {
        $featurit = new Featurit(self::TENANT_IDENTIFIER, self::VALID_API_KEY);

        $featureFlags = $featurit->featureFlags()->all();

        $allOfTheValuesAreBooleans = true;
        foreach($featureFlags as $featureFlagName => $isActive) {
            if (!is_bool($isActive)) {
                $allOfTheValuesAreBooleans = false;
                break;
            }
        }

        $this->assertTrue($allOfTheValuesAreBooleans);
    }

    public function test_is_active_returns_false_if_feature_name_doesnt_exist(): void
    {
        $featurit = new Featurit(self::TENANT_IDENTIFIER, self::VALID_API_KEY);

        $featureFlagValue = $featurit->featureFlags()->isActive(self::NON_EXISTING_FEATURE_NAME);

        $this->assertFalse($featureFlagValue);
    }

    public function test_is_active_returns_false_if_feature_name_exists_but_is_not_active(): void
    {
        $featurit = new Featurit(self::TENANT_IDENTIFIER, self::VALID_API_KEY);

        $featureFlagValue = $featurit->featureFlags()->isActive(self::EXISTING_INACTIVE_FEATURE_NAME);

        $this->assertFalse($featureFlagValue);
    }

    public function test_is_active_returns_true_if_feature_name_exists_and_is_active(): void
    {
        $featurit = new Featurit(self::TENANT_IDENTIFIER, self::VALID_API_KEY);

        $featureFlagValue = $featurit->featureFlags()->isActive(self::EXISTING_ACTIVE_FEATURE_NAME);

        $this->assertTrue($featureFlagValue);
    }
}