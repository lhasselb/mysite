<?php
/**
 * Apply this extension to text editing form field such as TextareaField
 * to allow setting of ideal, min and max lengths. These are
 * soft limits only, to give CMS users an idea of target length.
 */
class TextTargetLengthExtension extends Extension
{
    /**
     * Set a target character length for text in a field.
     *
     * @param Int $idealCharCount the ideal number of characters for this text
     * @param Int $minCharCount (default: null) the minimum number of characters for this text
     * @param Int $maxCharCount (default: null) the maximum number of characters for this text
     * @return FormField
     */
    public function setTargetLength($idealCharCount, $minCharCount = null, $maxCharCount = null)
    {
        $field = $this->owner;
        $idealCharCount = (int)$idealCharCount;
        if (!$idealCharCount > 0) return $field;
        // Set defaults
        if ($minCharCount === null) $minCharCount = round($idealCharCount * .75);
        if ($maxCharCount === null) $maxCharCount = round($idealCharCount * 1.25);
        // Validate
        if (!($maxCharCount >= $idealCharCount && $idealCharCount >= $minCharCount)) return $field;
        // Activate
        $field->addExtraClass('target-length');
        $field->setAttribute('data-target-ideal-length', $idealCharCount);
        $field->setAttribute('data-target-min-length', $minCharCount);
        $field->setAttribute('data-target-max-length', $maxCharCount);
        Requirements::javascript(FRAMEWORK_DIR.'/thirdparty/jquery/jquery.js');
        Requirements::javascript(FRAMEWORK_DIR.'/thirdparty/jquery-entwine/dist/jquery.entwine-dist.js');
        Requirements::javascript('mysite/javascript/text-target-length.js');
        Requirements::css('mysite//css/text-target-length.css');
        return $field;
    }
}
